<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 23/05/2017
 * Time: 14:43
 */

namespace OC\UserBundle;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\UserBundle\Entity\User;

class LoadUser implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $listNames = array(
            'Alexandre',
            'Marine',
            'Jean-Philippe',
            'Cynthia',
            'Jean-Luc'
        );

        foreach ($listNames as $name){
            $user = new User();
            $user->setUsername($name);
            $user->setPassword($name);

            $user->setSalt('');
            $user->setRoles(array("ROLE_USER"));

            $manager->persist($user);
        }

        $manager->flush();
    }

}