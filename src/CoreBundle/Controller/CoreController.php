<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 15/05/2017
 * Time: 14:19
 */

namespace CoreBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CoreController extends Controller
{
    public function indexAction()
    {
        return $this->render(
            'CoreBundle:Core:index.html.twig'
        );
    }

}