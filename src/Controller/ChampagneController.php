<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\services\Helpers;
use App\services\PanierManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChampagneController extends AbstractController
{

    private $params;
    private $doctrine;
    private $security;
    private $db;
    private $session;
    private $app;
    private $userInfo;
    private $cartCount;
    private $cartManager;

    public function __construct(ContainerBagInterface $params, ManagerRegistry $doctrine, Security $security, RequestStack $requestStack, Helpers $app, PanierManager $cartManager){

        $this->params = $params;
        $this->doctrine = $doctrine;
        $this->db = $doctrine->getManager();
        $this->security = $security;
        $this->userInfo = $app->getUser();

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

    public function champagne(Helpers $app): Response
    {
        $champagnes = $this->doctrine->getRepository(Produit::class)->findAll();

        return $this->render('home/champagne.html.twig', [
            'bodyId' => $app->getBodyId('CHAMPAGNE_PAGE'),
            'userInfo' => $this->userInfo,
            'champagnes' => $champagnes,
            'cartCount' => $this->cartCount,
        ]);
    }

    public function detailsChampagne(Produit $champagne, Helpers $app): Response {
        return $this->render('home/detail-champagne.html.twig', [
            'bodyId' => $app->getBodyId('DETAIL_CHAMPAGNE_PAGE'),
            'userInfo' => $this->userInfo,
            'champagne' => $champagne,
            'cartCount' => $this->cartCount,
        ]);
    }

    public function modifierChampagne(Helpers $app, Produit $champagne, Request $request, EntityManagerInterface $em): Response {

        $form = $this->createForm(ProduitType::class, $champagne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $em->persist($champagne);
            $em->flush();

            return $this->redirectToRoute('app_champagne');
        }

        return $this->render('admin/dashboard.html.twig',[
            'ProduitForm' => $form->createView(),
            'bodyId'=> $app->getBodyId('ADMIN_DASHBOARD'),
            'userInfo' => $this->userInfo,
            'cartCount' => $this->cartCount,
        ]);
    }

    public function supprimerChampagne(Produit $champagne, Request $request, EntityManagerInterface $em): Response {
        // Penser au CSRF
        if ($this->isCsrfTokenValid('supprimer', $request->query->get('token', ''))) {
            $em->remove($champagne);
            $em->flush();
            return $this->redirectToRoute('app_champagne');
        } else {
            throw new BadRequestException('Token CSRF Invalide.');
        }
    }

}
