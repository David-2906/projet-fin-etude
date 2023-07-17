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
            

            foreach($cartService->getTotal() as $produit){

                $orderDetails = new OrderDetails();
                $orderDetails->setOrderProduct($order);
                $orderDetails->setQuantity($produit['quantity']);
                $orderDetails->setPrice($produit['produit']->getPrix() * $produit['quantity']);
                $orderDetails->setProduct($produit['produit']->getDesignation());
                $orderDetails->setTotalRecap(($produit['produit']->getPrix() * $produit['quantity']) + + $transporter->getPrice() );


                $this->em->persist($orderDetails);
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
