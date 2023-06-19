<?php

namespace App\services;

use App\Entity\Panier;
use App\Entity\PanierProduit;
use App\Entity\Produit;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use stdClass;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PanierManager {

    private $params;
    private $doctrine;
    private $security;
    private $db;
    private $session;
    private $app;
    private $user;

    public function __construct(ContainerBagInterface $params, ManagerRegistry $doctrine, Security $security, RequestStack $requestStack, Helpers $app){

        $this->params = $params;
        $this->doctrine = $doctrine;
        $this->db = $doctrine->getManager();
        $this->security = $security;
        $this->user = $app->getUser();

        $this->session = $requestStack->getSession();
    }

    public function addToCart( User $user, Produit $produit){

        if(!$produit || !$user ) {
            return false;
        }

        // On essaie de récupérer le panier de l'utilisateur s'il en a un

        $panier = $user->getPanier();

        if($panier) {
            // Si l'utilisateur a dejà un panier
            $panier->setUpdatedAt(new \DateTime('now'));
            // On récupère la collection d'article dans le panier
            $panierProduits = $panier->getArticle();

        if(!count($panierProduits)){
            $panierProduit = new PanierProduit();
            $panierProduit->setPanier($panier);
            $panierProduit->setProduit($produit);
            $panierProduit->setQuantity(1);
            $panier->addArticle($panierProduit);
            $this->session->set('cartCount', $panier->getCountArticle());
        } else {
            // On boucle pour trouver l'article dans le detail du panier
            $found = false;
            foreach ( $panierProduits as $article){
                if($article->getProduit()->getId() === $produit->getId()) {
                    $found = true;
                // si article trouvé, on incrémente la quantité
                $article->setQuantity($article->getQuantity()+1);
                $panier->addArticle($article);
                $this->db->persist($panier);
                $this->db->persist($article);
                $this->db->flush();
                $this->session->set('cartCount', $panier->getCountArticle());
                return true;              
                }
            }
        // sinon on créer une nouvelle ligne d'enregistrement dans le detail du panier
        if(!$found) {
            $panierProduit = new PanierProduit();
            $panierProduit->setPanier($panier);
            $panierProduit->setProduit($produit);
            $panierProduit->setQuantity(1);
            $panier->addArticle($panierProduit);
            $this->session->set('cartCount', $panier->getCountArticle());
        }
        }
        } else {
            // si l'utilisateur n'a pas de panier on le créer
            $panier = new Panier();
            // On créer une nouvelle ligne d'enregistrement
            $panierProduit = new PanierProduit();
            // On affecte
            $panier->setUser($user);
            $panier->setCreatedAt(new \DateTime('now'));
            $panier->setUpdatedAt(new \DateTime('now'));
            $panierProduit->setPanier($panier);
            $panierProduit->setProduit($produit);
            $panierProduit->setQuantity(1);
            $panier->addArticle($panierProduit);
            $this->session->set('cartCount', $panier->getCountArticle());
        }
         
        // On enregistre dans la base de donnée
        $this->db->persist($panier);
        $this->db->persist($panierProduit);
        $this->db->flush();
        $this->session->set('cartCount', $panier->getCountArticle());

        return true;
    }

    public function removeFromCart(User $user, Produit $produit){

        $panier = $user->getPanier();
        $articles = $panier->getArticle();
        $idToRemove = $produit->getId();
        foreach ($articles as $article){
            if ($article->getProduit()->getId() === $idToRemove) {
                $panier->removeArticle($article);
            }
        }

        $this->db->persist($panier);
        $this->db->flush();
        $this->session->set('cartCount', $panier->getCountArticle());

        // on reinitialise le tunnel de commande si le nombre d'article tombe à zero

        if( $panier->getCountArticle() === 0){
            $this->session->remove('step');
        }
    }

    public function getCartCount(User $user): int {
        
        // On récupère le panier de l'utilisateur
        $panier = $user->getPanier();

        // On retourne le nombre d'article dans le panier

        if ($panier) {
            return $panier->getCountArticle();
        } else {
            return 0;
        }
    }
    
    public function calculPanier(Panier $panier, float $tauxTva) : stdClass {
        
        // On récupère les lignes d'article du panier
        $articles = $panier->getArticle();
        // On initialise le total du panier
        $totalTTC = 0;
        // On boucle sur les articles pour trouver le prix total TTC
        foreach ($articles as $article){
            $totalTTC += $article->getProduit()->getPrix();
        }
        //On en déduit le total HT
        $totalHT = round($totalTTC / (($tauxTva / 100) + 1), 2, PHP_ROUND_HALF_UP);
        // On en déduit le montant de la TVA
        $totallTVA = $totalTTC - $totalHT;

        // si on est dans le tunnel de commande à l'etape 4

        if(3 === $this->session->get('step')) {
            $temp = [];
            foreach ($articles as $article) {
                $temp[$article->getId()] = $article->getProduit()->getPrix();
            }
            $this->session->set('lignesPanier', $temp);
        } elseif (null !== $this->session->get('lignesPanier')) {
            $this->session->remove('lignePanier');
        }

        // on définit un objet de données
        $out = new stdClass();
        $out->totalTTC = $totalTTC;
        $out->totalHT = $totalHT;
        $out->totalTVA = $totallTVA;
        $out->tauxTva = $tauxTva;

        // On renvoi cet objet
        return $out;

    }
}