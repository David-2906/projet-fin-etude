<?php

namespace App\Entity;

use App\Repository\CepageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CepageRepository::class)]
class Cepage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private $id = null;

    #[ORM\Column(type:"string", length: 255, nullable: false, options:["collation" => "utf8mb4_general_ci"])]
    private $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
