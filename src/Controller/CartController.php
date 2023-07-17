<?php

namespace App\Controller;

use App\Entity\Produit;
use App\services\CartService;
use App\services\Helpers;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{

    private $doctrine;
    private $security;
    private $db;
    private $session;
    private $app;
    private $userInfo;
    private $cartManager;

    public function __construct(ManagerRegistry $doctrine, Security $security, Helpers $app, RequestStack $requestStack){

        $this->doctrine = $doctrine;
        $this->db = $doctrine->getManager();
        $this->security = $security;
        $this->userInfo = $app->getUser();

        $this->session = $requestStack->getSession();
    }
    

    public function panier(CartService $cartService, Helpers $app): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cartService->getTotal(),
            'bodyId' => $app->getBodyId('PANIER_PAGE'),
            'userInfo' => $this->userInfo,
        ]);
    }

    public function add(CartService $cartService, Produit $produit): RedirectResponse {

        $cartService->addToCart($produit->getId());
        return $this->redirectToRoute('app_champagne');
    }

    public function decrease($id, CartService $cartService): RedirectResponse {

        $cartService->decrease($id);
        return $this->redirectToRoute('app_mon_panier');
    }

    public function deleteCartById($id, CartService $cartService): RedirectResponse {

        $cartService->deleteCart($id);
        return $this->redirectToRoute('app_mon_panier');
    }

    public function delete(CartService $cartService): RedirectResponse {
        
        $cartService->deleteAllCart();
        return $this->redirectToRoute('app_mon_panier');
    }
}
