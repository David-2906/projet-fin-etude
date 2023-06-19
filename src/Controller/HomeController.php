<?php

namespace App\Controller;

use App\services\Helpers;
use App\services\PanierManager;
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
    private $userInfo;
    private $app;
    private $cartCount;

    public function __construct(ContainerBagInterface $params, ManagerRegistry $doctrine, Security $security, RequestStack $requestStack, Helpers $app, PanierManager $cartManager){

        $this->params = $params;
        $this->doctrine = $doctrine;
        $this->db = $doctrine->getManager();
        $this->security = $security;
        $this->userInfo = $app->getUser();

        $this->session = $requestStack->getSession();
        if (null !== $this->userInfo->user) {
            if (null !== $this->session->get('cartCount')) {
                $this->cartCount = (int)$this->session->get('cartCount');
            } else {
                $this->session->set('cartCount', $cartManager->getCartCount($this->userInfo->user));
                $this->cartCount = (int)$this->session->get('cartCount');
            }
        }
    }

    public function index(Helpers $app): Response
    {

        return $this->render('home/index.html.twig', [
            'bodyId' => $app->getBodyId('HOME_PAGE'),
            'userInfo' => $this->userInfo,
            'cartCount' => $this->cartCount,
        ]);
    }
}
