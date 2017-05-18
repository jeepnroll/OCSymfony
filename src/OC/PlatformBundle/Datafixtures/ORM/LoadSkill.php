<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 16/05/2017
 * Time: 14:51
 */

namespace OC\PlatformBundle\Datafixtures\ORM;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Skill;

class LoadSkill implements FixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $names = array(
            "PhP",
            "Symfony",
            "C++",
            "C#",
            "Java",
            "PhotoShop",
            "Blender",
            "Bloc-Note",
            "Illustrator",
            "AngularJS",
            "JavaScript",
            "JQuery"
        );

        foreach ($names as $name){
            $skill = new Skill();
            $skill->setName($name);

            // Persistance
            $manager->persist($skill);
        }

        // Enregistrement
        $manager->flush();
    }
}