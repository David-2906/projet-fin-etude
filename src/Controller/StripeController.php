<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Stripe\Stripe;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use App\services\CartService;
use App\services\Helpers;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeController extends AbstractController

{

    private string $bodyId;
    private $app;
    private $db;
    private $userInfo;
    private $session;
    private EntityManagerInterface $em;
    private UrlGeneratorInterface $generator;


    public function __construct(ManagerRegistry $doctrine, Helpers $app, RequestStack $requestStack, EntityManagerInterface $em, UrlGeneratorInterface $generator)

    {

        $this->app = $app;
        $this->bodyId = $app->getBodyId('ORDER_CONFIRMATION');
        $this->db = $doctrine->getManager();
        $this->userInfo = $app->getUser();
        $this->em = $em;
        $this->generator = $generator;


        $this->session = $requestStack->getSession();
    

    }

    
    public function index($reference): RedirectResponse {


        $productStripe = [];

        $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);

       

        if (!$order) {
            return $this->redirectToRoute('app_mon_panier');
        }

        foreach ($order->getOrderDetails()->getValues() as $produit) {

            $productData = $this->em->getRepository(Produit::class)->findOneBy(['designation' => $produit->getProduct()]);

            $productStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $productData->getPrix()*100,
                    'product_data' => [
                        'name' => $produit->getProduct(),
                    ]
                    ],
                    'quantity' => $produit->getQuantity()
                ];
        }

            $productStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $order->getTransporterPrice()*100,
                    'product_data' => [
                        'name' => $order->getTransporterName()
                    ]
                    ],
                    'quantity' => 1, // Il n'y aura toujours qu'un seul transporteur choisi donc on set la quantity a 1
                ];

                Stripe::setApiKey($_ENV["STRIPE_SECRET"]);

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [[
                $productStripe
            ]],
            'mode' => 'payment',
            'success_url' => $this->generator->generate('app_stripe_success', [
                'reference' => $order->getReference()
            ], UrlGenerator::ABSOLUTE_URL),
            'cancel_url' => $this->generator->generate('app_stripe_fail', [
                'reference' => $order->getReference()
            ], UrlGeneratorInterface::ABSOLUTE_URL),

            ]);

            return new RedirectResponse($checkout_session->url);


    }

    public function stripeSuccess($reference, CartService $Service, SessionInterface $session): Response {

        $session->remove('cart');

        return $this->render('stripe/order_confirmation.html.twig', [
            'bodyId' => $this->bodyId,

            'userInfo' => $this->userInfo,
        ]);
    }

    public function stripeError($reference, CartService $service): Response {
        
        return $this->render('stripe/payment_failure.html.twig', [
            'bodyId' => $this->bodyId,

            'userInfo' => $this->userInfo,
        ]);
    }


}
