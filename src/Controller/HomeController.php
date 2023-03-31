<?php

namespace App\Controller;

use App\services\Helpers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
   
    public function index(Helpers $app): Response
    {

        return $this->render('home/index.html.twig', [
            'bodyId' => $app->getBodyId('HOME_PAGE'),
        ]);
    }
}
