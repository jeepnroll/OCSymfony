<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 20/05/2017
 * Time: 14:02
 */

namespace OC\PlatformBundle\Validator;


use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Symfony\Component\Validator\Constraint;


/**
 * Class Antiflood
 * @package OC\PlatformBundle\Validator
 * @Annotation
 */
class Antiflood extends Constraint
{
    public $message = "Vous avez déjà posté un message il y a moins de 15 secondes, merci d'attendre un peu.";

    public function validatedBy()
    {
        return'oc_platform_antiflood';
    }
}