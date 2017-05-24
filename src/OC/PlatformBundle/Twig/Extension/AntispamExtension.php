<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 23/05/2017
 * Time: 16:04
 */

namespace OC\PlatformBundle\Twig\Extension;

use OC\PlatformBundle\Antispam\OCAntispam;
use Twig_Extension;


class AntispamExtension extends Twig_Extension
{
    /**
     * @var OCAntispam
     */
    private $ocAntispam;

    public function __construct(OCAntispam $ocAntispam)
    {
        $this->ocAntispam = $ocAntispam;
    }

    public function checkIdArgumentIsSpam($text){
        return $this->ocAntispam->isSpam($text);
    }

    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction('checkIfSpam', array($this, 'checkIfArgumentIsSpam')),
        );
    }

    public function getName(){
        return 'OCAntispam';
    }


}