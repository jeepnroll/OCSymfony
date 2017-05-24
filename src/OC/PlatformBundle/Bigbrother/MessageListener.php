<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 24/05/2017
 * Time: 11:02
 */

namespace OC\PlatformBundle\Bigbrother;


use OC\PlatformBundle\Event\MessagePostEvent;

class MessageListener
{
    protected $notificator;
    protected $listUsers = array();

    /**
     * MessageListener constructor.
     * @param $notificator
     * @param array $listUsers
     */
    public function __construct(MessageNotificator $notificator, array $listUsers)
    {
        $this->notificator = $notificator;
        $this->listUsers = $listUsers;
    }


    public function processMessage(MessagePostEvent $event){
        if(in_array($event->getUser()->getUsername(), $this->listUsers)){
            $this->notificator->notifyByEmail($event->getMessage(), $event->getUser());
        }
    }




}