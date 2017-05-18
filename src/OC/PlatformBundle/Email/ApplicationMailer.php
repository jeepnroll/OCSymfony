<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 17/05/2017
 * Time: 09:08
 */

namespace OC\PlatformBundle\Email;


use OC\PlatformBundle\Entity\Application;
use Swift_Mailer;

class ApplicationMailer
{
    /**
     * @var
     * \SwiftMailer
     * @param Swift_Mailer $mailer
     */
    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendNewNotification(Application $application){
        $message = new \Swift_Mailer(
          'Nouvelle Candidature',
          'Vous avez reçu une nouvelle candidature.'
        );

        $message->addTo($application->getAdvert()->getAuthorMail())
        ->addFrom("labo@zik-n-com.net");

        $this->mailer->send($message);
    }
}