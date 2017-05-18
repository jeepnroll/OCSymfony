<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 16/05/2017
 * Time: 13:21
 */

namespace OC\PlatformBundle\Datafixtures\ORM;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Category;

class LoadCategory implements FixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $names = array(
            "Développement Web",
            "Développement Mobile",
            "Graphisme",
            "Integration",
            "Réseau",
            "Analyst Développeur"
        );
        foreach ($names as $name){
            $category = new Category();
            $category->setName($name);

            $manager->persist($category);
        }

        // On déclenche l'enregistrement des données
        $manager->flush();
    }
}