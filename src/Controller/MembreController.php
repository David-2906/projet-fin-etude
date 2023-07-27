<?php

namespace App\Controller;

use App\Entity\Order;
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

        ]);
    }

    public function detailsCompte(Helpers $app) : Response {
        
        return $this->render('membre/details-compte.html.twig',[
            'bodyId'=>$app->getBodyId('DETAILS_MEMBRE'),
            'userInfo' => $this->user,
        ]);
    }

    public function modifierCompte(Helpers $app, Request $request, EntityManagerInterface $em) : Response {

        $user = $app->getUser()->user;

        $form = $this->createForm(ModifierCompteMembreType::class,$user);
        $form = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                'Votre compte à été modifié avec succès !'
            );

            return $this->redirectToRoute('app_details_membre');
    }
        return $this->render('membre/modifier-compte.html.twig', [
            'modifierForm' => $form->createView(),
            'bodyId' => $app->getBodyId('REGISTRATION'),
            'userInfo' => $this->user,
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

    public function mesCommandes(Helpers $app, ManagerRegistry $doctrine): Response {
        
        $user = $this->getUser();

        // Gérer le cas où l'utilisateur n'est pas connecté
        if(!$user){
            return $this->redirectToRoute('app_login');
        }

         // Récupérer les commandes de l'utilisateur
        $commandes = $doctrine->getManager()->getRepository(Order::class)->findBy(['user' => $user], ['createdAt' => 'DESC']);

        return $this->render('membre/mes-commandes.html.twig', [
            'commandes' => $commandes,
            'bodyId' => $app->getBodyId('MES_COMMANDES'),
            'userInfo' => $this->user,         
        ]);
    }
}
