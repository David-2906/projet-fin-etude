<?php

namespace App\Controller;

use App\services\Helpers;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $params;
    private $doctrine;
    private $security;
    private $db;
    private $session;
    private $user;
    private $app;

    public function __construct(ContainerBagInterface $params, ManagerRegistry $doctrine, Security $security, RequestStack $requestStack, Helpers $app){

        $this->params = $params;
        $this->doctrine = $doctrine;
        $this->db = $doctrine->getManager();
        $this->security = $security;
        $this->user = $app->getUser();

        $this->session = $requestStack->getSession();
    }

    public function index(Helpers $app): Response
    {

        return $this->render('home/index.html.twig', [
            'bodyId' => $app->getBodyId('HOME_PAGE'),
            'userInfo' => $this->user,
        ]);
    }
}
