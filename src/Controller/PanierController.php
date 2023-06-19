<?php

namespace App\Controller;

use App\Entity\Produit;
use App\services\Helpers;
use App\services\PanierManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    private $params;
    private $doctrine;
    private $security;
    private $db;
    private $session;
    private $app;
    private $userInfo;
    private $cartManager;
    private $cartCount;

    public function __construct(ContainerBagInterface $params, ManagerRegistry $doctrine, Security $security, RequestStack $requestStack, Helpers $app, PanierManager $cartManager){

        $this->app = $app;
        $this->params = $params;
        $this->doctrine = $doctrine;
        $this->db = $doctrine->getManager();
        $this->security = $security;
        $this->userInfo = $app->getUser();
        $this->cartManager = $cartManager;

        $this->session = $requestStack->getSession();

        if (null !== $this->userInfo->user) {
            if(null !== $this->session->get('cartCount')) {
                $this->cartCount = (int)$this->session->get('cartCount');
            } else {
                $this->session->set('cartCount', $cartManager->getCartCount($this->userInfo->user));
                $this->cartCount = (int)$this->session->get('cartCount');
            }
        }
    }

    // Ajout ajax d'un article dans le panier
    public function panier(Helpers $app, Request $request): JsonResponse
    {

        $produitId = $request->get('produitId');
        $produit = $this->db->getRepository(Produit::class)->find($produitId);

        if ($produit){
            if ($this->cartManager->addToCart($this->userInfo->user, $produit)) {
                return $this->json(['success' => true,]);
            }
        }

        return $this->json(['success' => false,]);



    }

    public function cartDetails(PanierManager $cartManager, Request $request, Helpers $app) : Response{
        
        // On vérifie si le bouton de suppresion à été cliqué
        if (null !== $request->get('id-to-delete')) {
            $idToDelete = $request->get('id-to-delete');
            $produit = $this->db->getRepository(Produit::class)->find($idToDelete);

            if($produit){
                $this->cartManager->removeFromCart($this->userInfo->user, $produit);
                $this->cartCount = $this->session->get('cartCount');
            }
        }
        // On récupère les articles du panier
        
        $articles = $this->userInfo->user->getPanier()->getArticle();
        // On définit le taux de TVA
        $tauxTva = 20;

        // on calcul le total du panier
  
       $totalPanier = $cartManager->calculPanier($this->userInfo->user->getPanier(), $tauxTva);
        
        // On récupère l'etat d'avancement  dans le tunnel de commande

        if(null !== $this->session->get('step')) {
            $step = $this->session->get('step');
        } else {
            $step = 1;
            $this->session->get('step', 1);
        }
        
        return $this->render('home/panier.html.twig', [
            'bodyId' => $app->getBodyId('PANIER_PAGE'),
            'userInfo' => $this->userInfo,
            'cartCount' => $this->cartCount,
            'articles' => $articles,
            'totalPanier' => $totalPanier,
            'step' => $step,
            'tableHeading' => ['Article', 'Désignation', 'Couleur', 'Taille', 'Qté', 'Prix (€)'],
        ]);
    }
}
