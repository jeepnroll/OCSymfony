<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 17/05/2017
 * Time: 09:17
 */

namespace OC\PlatformBundle\DoctrineListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use OC\PlatformBundle\Email\ApplicationMailer;
use OC\PlatformBundle\Entity\Application;

class ApplicationCreationListener
{
    /**
     * @var ApplicationMailer
     */
    private $applicationMailer;

    /**
     * ApplicationCreationListener constructor.
     * @param $applicationMailer
     */
    public function __construct(ApplicationMailer $applicationMailer)
    {
        $this->applicationMailer = $applicationMailer;
    }

    public function postPersist(LifecycleEventArgs $args){
        $entity = $args->getObject();

        // On peut envoyer un mail qui pour les entités Application
        if(!$entity instanceof Application){
            return;
        }

        $this->applicationMailer->sendNewNotification($entity);
    }

}