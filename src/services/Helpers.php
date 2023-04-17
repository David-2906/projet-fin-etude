<?php

namespace App\services;

use Doctrine\Persistence\ManagerRegistry;
use stdClass;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBag;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class Helpers{

    private $params;
    private $doctrine;
    private $security;
    private $db;

    public function __construct(ContainerBagInterface $params, ManagerRegistry $doctrine, Security $security){

        $this->params = $params;
        $this->doctrine = $doctrine;
        $this->db = $doctrine->getManager();
        $this->security = $security;
    }

    public function getBodyId(string $page): string {

        return $this->params->get($page);
    }

    public function getUser()
  {
    $user = $this->security->getUser();
    if ($user) {
      $isLoggedIn = true;
    } else {
      $isLoggedIn = false;
    }
    if ($this->security->isGranted('ROLE_ADMIN')) {
      $isAdmin = true;
    } else {
      $isAdmin = false;
    }
    $userObj = new stdClass();
    $userObj->user = $user;
    $userObj->isAdmin = $isAdmin;
    $userObj->isLoggedIn = $isLoggedIn;
    return $userObj;
  }
}