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

class ConnexionController extends AbstractController
{
    private $params;
    private $doctrine;
    private $security;
    private $db;
    private $session;
    private $app;
    private $userInfo;

    public function __construct(ContainerBagInterface $params, ManagerRegistry $doctrine, Security $security, RequestStack $requestStack, Helpers $app){

        $this->params = $params;
        $this->doctrine = $doctrine;
        $this->db = $doctrine->getManager();
        $this->security = $security;
        $this->userInfo = $app->getUser();

        $this->session = $requestStack->getSession();
    }

    public function connexion(Helpers $app): Response
    {
        return $this->render('home/connexion.html.twig', [
            'bodyId' => $app->getBodyId('CONNEXION_PAGE')
        ]);
    }
}
