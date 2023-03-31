<?php

namespace App\Controller;

use App\services\Helpers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticController extends AbstractController
{
    public function mentionsLegales(Helpers $app): Response
    {
        return $this->render('static/mentions.html.twig', [
            'bodyId' => $app->getBodyId('LEGAL_PAGE'),
        ]);
    }

    public function politiqueCookies(Helpers $app): Response
    {
        return $this->render('static/cookies.html.twig', [
            'bodyId' => $app->getBodyId('COOKIE_PAGE'),
        ]);
    }
}
