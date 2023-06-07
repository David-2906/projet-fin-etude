<?php

namespace App\Controller;

use App\services\Helpers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
   
    public function commande(Helpers $app): Response
    {
        return $this->render('commande/index.html.twig', [
            'bodyId'=>$app->getBodyId('COMMANDE_PAGE'),
        ]);
    }
}
