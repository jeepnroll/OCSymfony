<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 22/05/2017
 * Time: 09:47
 */

namespace OC\PlatformBundle\Purger;


use Doctrine\ORM\EntityManagerInterface;

class AdvertPurger
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * AdvertPurger constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function purge($days)
    {
        $advertRepo =   $this->em->getRepository('OCPlatformBundle:Advert');
        $advertSkillRepo = $this->em->getRepository("OCPlatformBundle:AdvertSkill");

        $date = new \DateTime($days. " days ago");

        $listAdverts = $advertRepo->getAdvertsBefore($date);

        foreach ($listAdverts as $advert){
            $advertSkills = $advertSkillRepo->findBy(array("advert" => $advert));

            foreach ($advertSkills as $advertSkill){
                $this->em->remove($advertSkill);
            }

            $this->em->remove($advert);
        }

        $this->em->flush();

    }

}