<?php

namespace App\services;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBag;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class Helpers{

    public function __construct(private ContainerBagInterface $param){

    }

    public function getBodyId(string $page): string {

        return $this->param->get($page);
    }
}