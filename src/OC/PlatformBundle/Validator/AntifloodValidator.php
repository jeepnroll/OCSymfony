<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 20/05/2017
 * Time: 14:05
 */

namespace OC\PlatformBundle\Validator;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AntifloodValidator extends ConstraintValidator
{

    private $requestStack;
    private $em;

    /**
     * AntifloodValidator constructor.
     * @param $requestStack
     * @param $em
     */
    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }


    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        $request = $this->requestStack->getCurrentRequest();
        $ip = $request->getClientIp();

        $isFlood = $this->em
            ->getRepository('OCPlatformBundle:Application')
            ->isFlood($ip, 15);

        if ($isFlood){
            $this->context->addViolation($constraint->message);
        }
    }

}