<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 13/05/2017
 * Time: 13:33
 */

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Category;
use OC\PlatformBundle\Event\MessagePostEvent;
use OC\PlatformBundle\Event\PlatformEvents;
use OC\PlatformBundle\Form\AdvertEditType;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * @property  container
 */
class AdvertController extends Controller
{

    /**
     * @param $limit
     * @return Response
     */
    public function menuAction($limit)
    {
        $em = $this->getDoctrine()->getManager();

        $listAdverts = $em->getRepository("OCPlatformBundle:Advert")->findBy(
            array(),                 // Pas de critères
            array('date' => "desc"), // On trie par date décroissante
            $limit,                  // On sélectionne le nombre d'annonce à afficher avec $limit
            0                  // A partir du premier
        ) ;

        return $this->render(
            "OCPlatformBundle:Advert:menu.html.twig",
            array(
                'listAdverts' => $listAdverts
            )
        );
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // Ici, on récupérera l'annonce correspondante à $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
        $addCat = new Category();
        // Même mécanisme que pour l'ajout
        if(null === $advert){
            throw new NotFoundHttpException("l'annonce d'id " . $id . " n'existe pas !");
        }
        $form = $this->get('form.factory')->create(AdvertEditType::class, $advert);
        $formAddCat = $this->get('form.factory')->create(CategoryType::class, $addCat);

        if($formAddCat->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($addCat);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', "Ajout de la catégorie bien enregistrée.");
        }


        if($request->isMethod("POST") && $form->handleRequest($request)->isValid()){

            $em->flush();
            $request->getSession()->getFlashBag()->add("notice", "Annonce bien modifiée");

            return $this->redirectToRoute("oc_platform_view", array("id" => $advert->getId()));
        }

        return $this->render(
            'OCPlatformBundle:Advert:edit.html.twig',
            array(
                'advert'    => $advert,
                'form'      => $form->createView(),
                'formAddCat'    => $formAddCat->createView()
            )
        );
    }


    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        // Ici, on récupérera l'annonce correspondant à $id
        $advert = $em->getRepository("OCPlatformBundle:Advert")->find($id);

        if(null === $advert){
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }

        $form = $this->get('form.factory')->create();

        if($request->isMethod("POST") && $form->handleRequest($request)->isValid()){
            $em->remove($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add("info", "L'annonce à bien été supprimée");

            return $this->redirectToRoute("oc_platform_home");
        }

        // Ici, on gérera la suppression de l'annonce en question
        return $this->render(
            'OCPlatformBundle:Advert:delete.html.twig',
            array(
                'advert' => $advert,
                "form"  => $form->createView()
            )
            );
    }


    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function addAction(Request $request)
    {
        // On vérifie que l'utilisateur dispose bien du rôle ROLE_AUTEUR
        if(!$this->get("security.authorization_checker")->isGranted('ROLE_AUTEUR')){
            throw new AccessDeniedException('Accès limité aux auteurs');
        }

        // On crée un objet Advert
        $advert = new Advert();
        $addCat = new Category();
        // On crée le FormBuilder grâce au service form factory
        $form = $this->get("form.factory")->create(AdvertType::class, $advert);
        $formAddCat = $this->get("form.factory")->create(CategoryType::class, $addCat)   ;
        // Si la requête est en POST

        if($formAddCat->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($addCat);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', "Ajout de la catégorie bien enregistrée.");
        }

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $event = new MessagePostEvent($advert->getContent(), $advert->getUser());
            $this->get('event_dispatcher')->dispatch(PlatformEvents::POST_MESSAGE, $event);
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur

            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add("notice", "Annonce bien enregistrée.");

            // On redirige vers la page de l'annonce nouvellement créée
            return $this->redirectToRoute("oc_platform_view", array("id" => $advert->getId()));
        }
        return $this->render(
            "OCPlatformBundle:Advert:add.html.twig",
            array(
                'form' => $form->createView(),
                'formAddCat' => $formAddCat->createView()
            )
        );
    }

    /**
     * Affichage de l'annonce n° $id
     * @param $id
     * @return Response
     */
    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository("OCPlatformBundle:Advert")->find($id);

        if(null === $advert){
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }

        // On récupère la liste des candidatures de cette annonce
        $listApp = $em->getRepository("OCPlatformBundle:Application")
            ->findBy(array("advert" => $advert));

        // On récupère la liste des AdvertSkill
        $listAdvertSkill = $em
            ->getRepository('OCPlatformBundle:AdvertSkill')
            ->findBy(array("advert" => $advert));

        return $this->render(
            'OCPlatformBundle:Advert:view.html.twig',
            array(
                "advert" => $advert,
                "listApp" => $listApp,
                "listAdvertSkill" => $listAdvertSkill
            )
        );
    }

    /**
     * Méthode indexAction => Affichage de la plate-forme
     * @param $page
     * @return Response
     */
    public function indexAction($page)
    {
        if($page < 1) {
            throw new NotFoundHttpException("Page " . $page . " inexistante.");
        }
        // Ici je fixe le nombre d'annonces par page à 3
        // Mais bien sûr il faudrait utiliser un paramètre,
        // et y accéder via $this->container->getParameter('nb_per_page')
        $nbPerPage = 3;

        // On récupère notre objet Paginator
        $listAdverts = $this->getDoctrine()
            ->getManager()
            ->getRepository("OCPlatformBundle:Advert")
            ->getAdverts($page, $nbPerPage);

        // On calcul le nombre total de pages grâce au count($listAdverts)
        // qui retourne le nombre total d'annonce
        $nbPages = ceil(count($listAdverts) / $nbPerPage);

        // Si la page n'existe pas, on retourne un erreur 404
        if($page > $nbPages){
            throw $this->createNotFoundException("la page " . $page . " n'existe pas");
        }

        return $this->render(
            'OCPlatformBundle:Advert:index.html.twig',
            array(
                'listAdverts'   => $listAdverts,
                'nbPages'        => $nbPages,
                'page'          => $page
            )
        );
    }

    public function purgeAction($days, Request $request)
    {
        $purger = $this->get("oc_platform.purger.advert");
        $purger->purge($days);

        $request->getSession()->getFlashBag()->add('info', 'Les annonces plus vielles que ' . $days . ' jours ont été purgées.');

        return $this->redirectToRoute('oc_platform_home');
    }

    public function translationAction($name){
        return $this->render(
            'OCPlatformBundle:Advert:translation.html.twig',
            array(
                "name" => $name
            )
        );
    }
}