<?php

namespace App\Controller;

use App\Entity\Cepage;
use App\Entity\Produit;
use App\Entity\ResetPasswordRequest;
use App\Entity\TypeProduit;
use App\Entity\User;
use App\Form\CepageType;
use App\Form\ModifierCompteMembreType;
use App\Form\ProduitType;
use App\Form\RegistrationFormType;
use App\Form\TypeProductType;
use App\services\Helpers;
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

class AdminController extends AbstractController
{
    private $params;
    private $doctrine;
    private $security;
    private $db;
    private $session;
    private $user;
    private $app;

    public function __construct(ContainerBagInterface $params, ManagerRegistry $doctrine, Security $security, RequestStack $requestStack, Helpers $app){

        $this->params = $params;
        $this->doctrine = $doctrine;
        $this->db = $doctrine->getManager();
        $this->security = $security;
        $this->user = $app->getUser();

        $this->session = $requestStack->getSession();
    }

    public function dashboard(Helpers $app, Request $request,EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit;
        $form = $this->createForm(ProduitType::class,$produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($produit);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/dashboard.html.twig', [
            'ProduitForm' => $form->createView(),
            'bodyId'=>$app->getBodyId('ADMIN_DASHBOARD'),
            'userInfo' => $this->user,
        ]);
    }

    public function addCepage(Helpers $app, Request $request, EntityManagerInterface $entityManager): Response
    {
        $cepage = new Cepage;
        $form = $this->createForm(CepageType::class, $cepage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($cepage);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_cepage');
        }

        return $this->render('admin/cepage-dashboard.html.twig', [
            'CepageForm' => $form->createView(),
            'bodyId'=>$app->getBodyId('ADMIN_CEPAGE'),
            'userInfo' => $this->user,
        ]);
    }

    public function addType(Helpers $app, Request $request, EntityManagerInterface $entityManager): Response
    {
        $typeProduit = new TypeProduit;
        $form = $this->createForm(TypeProductType::class, $typeProduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($typeProduit);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_typeProduit');
        }

        return $this->render('admin/typeProduit-dashboard.html.twig', [
            'TypeProduitForm' => $form->createView(),
            'bodyId'=>$app->getBodyId('ADMIN_TYPE_PRODUIT'),
            'userInfo' => $this->user,
        ]);
    }

    public function membreManagement(Helpers $app): Response {
        
        $members = $this->doctrine->getRepository(User::class)->findAll();

        return $this->render('admin/membre-management.html.twig', [
            'bodyId' => $app->getBodyId('ADMIN_MEMBERS_MANAGEMENT'),
            'userInfo' => $this->user,
            'members' => $members,
        ]);
    }

    public function modifierMembre(Helpers $app, Request $request, EntityManagerInterface $em, User $member): Response {
        $form = $this->createForm(ModifierCompteMembreType::class, $member);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($member);
            $em->flush();

            return $this->redirectToRoute('app_membre_management');
        }

        return $this->render('membre/modifier-compte.html.twig',[
            'modifierForm' => $form->createView(),
            'bodyId' => $app->getBodyId('REGISTRATION'),
            'membre' => $member,
            'userInfo' => $this->user,
        ]);
    }

    public function supprimerMembre(User $member,Request $request, EntityManagerInterface $em): Response {
        if ($this-> isCsrfTokenValid('supprimer', $request->query->get('token', ''))) {
            $em->remove($member);
            $em->flush();
            return $this->redirectToRoute('app_membre_management');
        } else {
            throw new BadRequestException('Token CSRF Invalide');
        }
    }

}
