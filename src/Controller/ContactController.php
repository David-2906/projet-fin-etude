<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\services\Helpers;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;



class ContactController extends AbstractController
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

    
    public function contact(Helpers $app, Request $request, EntityManagerInterface $manager, MailerInterface $mailer): Response
    {
        $contact = new Contact();

        if($this->getUser()) {
            $contact->setNom($this->getUser()->getNom())
                    ->setPrenom($this->getUser()->getPrenom())
                    ->setEmail($this->getUser()->getEmail());
        }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $contact = $form->getData();

            $manager->persist($contact);
            $manager->flush();

            // Envoi sur la boite mail

            $email = (new TemplatedEmail())
            ->from($contact->getEmail())
            ->to('david.pires2906@gmail.com')
            ->subject($contact->getSujet())
            ->htmlTemplate('emails/contact.html.twig')
            ->context([
                    'contact' => $contact
                   ]);

        $mailer->send($email);

            $this->addFlash(
                'success',
                'Votre message à été envoyé avec succès !'
            );

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
            'bodyId' => $app->getBodyId('CONTACT'),
            'userInfo' => $this->user,
        ]);
    }
}
