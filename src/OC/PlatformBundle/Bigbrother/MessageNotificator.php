<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 24/05/2017
 * Time: 10:53
 */

namespace OC\PlatformBundle\Bigbrother;


use Symfony\Component\Security\Core\User\UserInterface;

class MessageNotificator
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * MessageNotificator constructor.
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function notifyByEmail($message, UserInterface $user){
        $message = \Swift_Message::newInstance()
            ->setSubject("Nouveau message d'un utilisateur surveillé")
            ->setFrom('labo@zik-n-com.net')
            ->setTo("labo@zik-n-com.net")
            ->setBody("L'utilisateur surveillé « " . $user->getUsername() . " » a posté le message suivant :" . $message);

        $this->mailer->send($message);
    }

}