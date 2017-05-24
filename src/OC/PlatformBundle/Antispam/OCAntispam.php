<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 15/05/2017
 * Time: 12:35
 */

namespace OC\PlatformBundle\Antispam;


class OCAntispam
{
    private $mailer;
    private $locale;
    private $minLength;

    /**
     * OCAntispam constructor.
     * @param $mailer
     * @param $minLength
     */
    public function __construct(\Swift_Mailer $mailer, $minLength)
    {
        $this->mailer = $mailer;
        $this->minLength = (int) $minLength;
    }


    public function isSpam($text)
    {
        return strlen($text) < $this->minLength;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}