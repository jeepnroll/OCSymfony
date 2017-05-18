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
     * @param $locale
     * @param $minLength
     */
    public function __construct(\Swift_Mailer $mailer, $locale, $minLength)
    {
        $this->mailer = $mailer;
        $this->locale = $locale;
        $this->minLength = (int) $minLength;
    }


    public function isSpam($text)
    {
        return strlen($text) < $this->minLength;
    }
}