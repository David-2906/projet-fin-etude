<?php

namespace App\Controller;

use App\services\Helpers;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticController extends AbstractController
{

    private $params;
    private $doctrine;
    private $security;
    private $db;
    private $session;

    public function __construct(ContainerBagInterface $params, ManagerRegistry $doctrine, Security $security, RequestStack $requestStack){

        $this->params = $params;
        $this->doctrine = $doctrine;
        $this->db = $doctrine->getManager();
        $this->security = $security;

        $this->session = $requestStack->getSession();
    }

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

    public function about(Helpers $app): Response {
        return $this->render('static/about.html.twig',[
            'bodyId' => $app->getBodyId('ABOUT_PAGE'),
        ]);
    }

    public function cgv(Helpers $app): Response {
        return $this->render('static/cgv.html.twig',[
            'bodyId' => $app->getBodyId('CGV_PAGE'),
        ]);
    }

    public function utilisation(Helpers $app): Response {
        return $this->render('static/utilisation.html.twig',[
            'bodyId' => $app->getBodyId('UTILISATION_PAGE'),
        ]);
    }
}
