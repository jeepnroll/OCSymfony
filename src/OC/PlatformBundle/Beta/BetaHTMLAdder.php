<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 23/05/2017
 * Time: 17:10
 */

namespace OC\PlatformBundle\Beta;


use Symfony\Component\HttpFoundation\Response;

class BetaHTMLAdder
{
    public function addBeta(Response $response, $remainingDays)
    {
        $content = $response->getContent();
        $html = '<div class="fixed-top p-3 text-center m-0 bg-warning lead">Beta J-'. (int) $remainingDays . ' !</div>';

        $content = str_replace(
            "<body>",
            "<body> " . $html,
            $content
            );

        $response->setContent($content);

        return $response;
    }
}