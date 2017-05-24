<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 23/05/2017
 * Time: 17:17
 */

namespace OC\PlatformBundle\Beta;


use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BetaListener
{
    protected $betaHTML;

    protected $endDate;

    /**
     * BetaListener constructor.
     * @param $betaHTML
     * @param $endDate
     */
    public function __construct(BetaHTMLAdder $betaHTML, $endDate)
    {
        $this->betaHTML = $betaHTML;
        $this->endDate = new \DateTime($endDate);
    }

    public function ignoreBeta(FilterControllerEvent $event)
    {
        if(!$event->isMasterRequest()){
            return;
        }
    }

    public function processBeta(FilterResponseEvent $event)
    {
        if(!$event->isMasterRequest()){
            return;
        }

        $today = new \DateTime();

        $remainingDays = $this->endDate->diff($today)->days;

        if($remainingDays <= 0){
            return;
        }


        $response = $this->betaHTML->addBeta($event->getResponse(), $remainingDays);

        $event->setResponse($response);

        $event->stopPropagation();
    }
}