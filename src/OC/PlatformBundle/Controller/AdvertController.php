<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 13/05/2017
 * Time: 13:33
 */

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Repository\AdvertRepository;


/**
 * @property  container
 */
class AdvertController extends Controller
{

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

        // Même mécanisme que pour l'ajout
        if(null === $advert){
            throw new NotFoundHttpException("l'annonce d'id " . $id . " n'existe pas !");
        }

        $listCategories = $em->getRepository("OCPlatformBundle:Category")->findAll();

        foreach ($listCategories as $category){
            $advert->addCategory($category);
        }

        $em->flush();

        if ($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');
            return $this->redirectToRoute('oc_platform_view', array('id' => 5));
        }

        return $this->render(
            'OCPlatformBundle:Advert:edit.html.twig',
            array(
                'advert' => $advert
            )
        );
    }


    /**
     * @param $id
     * @return Response
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        // Ici, on récupérera l'annonce correspondant à $id
        $advert = $em->getRepository("OCPlatformBundle:Advert")->find($id);

        if(null === $advert){
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }

        foreach ($advert->getCategories() as $category){
            $advert->removeCategory($category);
        }
        // Ici, on gérera la suppression de l'annonce en question
        return $this->render('OCPlatformBundle:Advert:delete.html.twig');
    }


    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function addAction(Request $request)
    {
        // On crée un objet Advert
        $advert = new Advert();
        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get("form.factory")->createBuilder(FormType::class, $advert);

        // On ajoute les champs de l'entité que l'on veut dans notre formulaire
        $formBuilder
            ->add("date"            , DateType::class       )
            ->add("title"           , TextType::class       )
            ->add("author"          , TextType::class       )
            ->add("authorMail"      , EmailType::class      )
            ->add("content"         , TextareaType::class   )
            ->add("finderProfil"    , TextareaType::class   )
            ->add("published"       , CheckboxType::class   )
            ->add("save"            , SubmitType::class     )
        ;
        // Pour l'instant, pas de candidatures, catégories, etc., on les gérera plus tard

        // À partir du formBuilder, on génère le formulaire
        $form = $formBuilder->getForm();
        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule

        // Si la requête est en POST
        if($request->isMethod('POST'))
        {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);
            if($form->isValid()){
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
                )
            );
        }

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
                'nbPage'        => $nbPages,
                'page'          => $page
            )
        );
    }
}