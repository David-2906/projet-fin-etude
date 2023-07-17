<?php

namespace App\services;

use App\Entity\Produit;
use App\services\Helpers;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService {

    private RequestStack $requestStack;
    private EntityManagerInterface $em;
    private $doctrine;
    private $db;
    private $user;
    private $security;
    private $session;


    public function __construct(RequestStack $requestStack, EntityManagerInterface $em, Helpers $app, ManagerRegistry $doctrine, Security $security){

        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->doctrine = $doctrine;
        $this->security = $security;
        $this->db = $doctrine->getManager();
        $this->user = $app->getUser();

        $this->session = $requestStack->getSession();

    }


    public function addToCart(int $id){

         // recuperation du tableau de produit ajoutés au panier depuuis le session utilisateur
         $cart = $this->session->get('cart',[]);

         if(!empty($cart[$id])){
            $cart[$id]++;
         } else {
            $cart[$id] = 1;
         }

         $this->session->set('cart',$cart);
    }

    public function getTotal(){

        // recuperation du tableau de produits ajoutés au panier depuis la session utilisateur
        $cart = $this->session->get('cart');
        $cartData = [];

        if($cart){
            foreach($cart as $id => $q){
                //recuperation du produit a partir de son id en bdd
                $fetchProduit = $this->em->getRepository(Produit::class)->findOneBy(['id'=>$id]);
                if($fetchProduit){
                    $cartData[]=[
                        'produit' => $fetchProduit,
                        'quantity' => $q,
                    ];
                }
            }
        }
            //return le tableau de produits avec leurs informations et quantités
            return $cartData;
    }

    public function decrease($id){

             // recuperation du tableau de produit ajouter au panier depuis la sesion utilisateur
             $cart = $this->session->get('cart',[]);

             // verification si la quantité du produit est superieur a 1 pour pouvoir decrementer
             if($cart[$id] > 1) {
                    $cart[$id]--;
             } else {
                // si la quantité du produit est egale a 1 on supprime le produit du panier
                unset($cart[$id]);
             }

             return $this->session->set('cart', $cart);
    }

    public function deleteCart($id){

            // recuperation du tableau de produit ajouter au panier depuis la session utilisateur
            $cart = $this->session->get('cart',[]);
            // suppression du produit qui est dans le panier
            unset($cart[$id]);
            return $this->session->set('cart', $cart);
    }

    public function deleteAllCart(){

        return $this->session->remove('cart');
    }
}