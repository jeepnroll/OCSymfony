<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 24/05/2017
 * Time: 10:13
 */

namespace OC\PlatformBundle\Event;


use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class MessagePostEvent extends Event
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * MessagePostEvent constructor.
     * @param $message
     * @param $user
     */
    public function __construct($message, UserInterface $user)
    {
        $this->message = $message;
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }






}