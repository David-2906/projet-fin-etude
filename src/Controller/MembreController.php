<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModifierCompteMembreType;
use App\Form\RegistrationFormType;
use App\services\Helpers;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class MembreController extends AbstractController
{
    private string $bodyId;
    private $user;
    private $cartCount;

    public function __construct(Helpers $app)
    {
        $this->bodyId = $app->getBodyId('MEMBER_DASHBOARD');
        $this->user = $app->getUser();
        
    }


    public function dashboard(Helpers $app): Response
    {
        return $this->render('membre/dashboard.html.twig', [
            // 'controller_name' => 'MembreController',
            'bodyId' => $app->getBodyId('MEMBER_DASHBOARD'),
            'userInfo' => $this->user,
            'cartCount' => $this->cartCount,
        ]);
    }

    public function detailsCompte(Helpers $app) : Response {
        
        return $this->render('membre/details-compte.html.twig',[
            'bodyId'=>$app->getBodyId('DETAILS_MEMBRE'),
            'userInfo' => $this->user,
            'cartCount' => $this->cartCount,
        ]);
    }

    public function modifierCompte(Helpers $app, Request $request, EntityManagerInterface $em) : Response {

        $user = $app->getUser()->user;

        $form = $this->createForm(ModifierCompteMembreType::class,$user);
        $form = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_details_membre');
    }
        return $this->render('membre/modifier-compte.html.twig', [
            'modifierForm' => $form->createView(),
            'bodyId' => $app->getBodyId('REGISTRATION'),
            'userInfo' => $this->user,
            'cartCount' => $this->cartCount,
        ]);

    }

    public function supprimerCompte($id, Helpers $app, EntityManagerInterface $em, ManagerRegistry $doctrine, Request $request, Session $session): Response {

        if($id){
            $session = new Session;
            $session->invalidate();
            $membre = $doctrine->getManager()->getRepository(User::class)->find($id);
            $doctrine->getManager()->remove($membre);
            $doctrine->getManager()->flush();
        };

        return $this->redirectToRoute('app_home');
    }
}
