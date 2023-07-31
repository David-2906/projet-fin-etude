<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use App\services\CartService;
use App\services\Helpers;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
 
    private $params;
    private $doctrine;
    private $security;
    private $db;
    private $session;
    private $app;
    private $userInfo;
    private $cartManager;
    private $em;

    public function __construct(ContainerBagInterface $params, ManagerRegistry $doctrine, Security $security, RequestStack $requestStack, Helpers $app, EntityManagerInterface $em)
    {
        $this->params = $params;
        $this->doctrine = $doctrine;
        $this->db = $doctrine->getManager();
        $this->security = $security;
        $this->userInfo = $app->getUser();
        $this->em = $em;

        $this->session = $requestStack->getSession();
    }


    public function commande(Helpers $app, CartService $cartService): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        
        $form = $this->createForm(OrderType::class, [
            'userInfo' => $this->getUser()
        ]);

        return $this->render('commande/index.html.twig', [
            'bodyId'=>$app->getBodyId('COMMANDE_PAGE'),
            'userInfo' => $this->userInfo,
            'form' => $form->createView(),
            'recapPanier' => $cartService->getTotal(),
        ]);
    }

    public function preparationCommande( Helpers $app, Request $request, CartService $cartService): Response {

        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(OrderType::class,[
            'userInfo' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $datetime = new \Datetime('now');
            $transporter = $form->get('transporteur')->getData();
            $order = new Order();
            $reference = $datetime->format('dmY').'-'.uniqid();
            $order->setReference($reference);
            $order->setUser($this->getUser());
            $order->setCreatedAt($datetime);
            $order->setTransporterName($transporter->getTitle());
            $order->setTransporterPrice($transporter->getPrice());
            $order->setIsPaid(0);

            $this->em->persist($order);
            
            $hasStockErrors = false;
            $stockErrors = [];

            foreach($cartService->getTotal() as $produit){

                $orderDetails = new OrderDetails();
                $orderDetails->setOrderProduct($order);
                $orderDetails->setQuantity($produit['quantity']);
                $orderDetails->setPrice($produit['produit']->getPrix() * $produit['quantity']);
                $orderDetails->setProduct($produit['produit']->getDesignation());
                $orderDetails->setTotalRecap(($produit['produit']->getPrix() * $produit['quantity']) );


                $this->em->persist($orderDetails);

                $product = $produit['produit'];
                $quantiteCommandee = $produit['quantity'];

                if ($quantiteCommandee > $product->getStock()) {
                    // Si la quantité commandée dépasse le stock disponible, mettez à jour la variable d'erreur et stockez les informations sur le produit
                    $hasStockErrors = true;
                    $stockErrors[] = [
                        'produit' => $product->getDesignation(), // Stockez le nom du produit avec une quantité commandée supérieure au stock
                        'stock_disponible' => $product->getStock(), // Stockez la quantité de stock disponible pour le produit
                        'quantite_commandee' => $quantiteCommandee, // Stockez la quantité commandée pour le produit
                    ];  
                    // Passez à l'élément suivant dans la boucle 
                        continue;
                } else { // Mettre à jour le stock comme prévu
                $nouveauStock = $product->getStock() - $quantiteCommandee;
                $product->setStock($nouveauStock);
                $this->em->persist($product);
                }
            }
            
            if ($hasStockErrors) {
                // S'il y a des erreurs, affichez un message d'erreur à l'utilisateur en incluant les informations sur les produits avec un stock insuffisant
                foreach ($stockErrors as $stockError) {
                    // Générez un message d'erreur spécifique pour chaque produit dont la quantité commandée dépasse le stock disponible en utilisant la fonction sprintf pour formater le message.
                    $message = sprintf(
                        'La quantité commandée pour le produit "%s" dépasse le stock disponible. Stock disponible : %d, Quantité commandée : %d',
                        // %s : Cette séquence de format est utilisée pour afficher des chaînes de caractères. Lorsque vous utilisez %s, la valeur fournie doit être une chaîne de caractères.
                        // %d : Cette séquence de format est utilisée pour afficher des entiers décimaux (valeurs numériques entières). Lorsque vous utilisez %d, la valeur fournie doit être un entier.
                        $stockError['produit'], // Utilisez le nom du produit à partir du tableau $stockErrors
                        $stockError['stock_disponible'], // Utilisez la quantité de stock disponible à partir du tableau $stockErrors
                        $stockError['quantite_commandee'] // Utilisez la quantité commandée à partir du tableau $stockErrors
                    );
                    
                    $this->addFlash('error', $message);
                }
                
                // Vous pouvez également rediriger l'utilisateur vers une page de retour au panier pour qu'il puisse corriger sa commande.
                return $this->redirectToRoute('app_mon_panier');
            }

            $this->em->flush();
            
        return $this->render('commande/recap.html.twig',[
            'bodyId'=>$app->getBodyId('COMMANDE_PAGE'),
            'userInfo' => $this->userInfo,
            'recapPanier' => $cartService->getTotal(),
            'transporteur' => $transporter,
            'reference' => $order->getReference(),
        ]);   

        }    
    }

    
}
