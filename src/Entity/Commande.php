<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id = null;

    #[ORM\Column(type:"integer", nullable: false)]
    private  $numero_commande = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private $date_commande = null;

    #[ORM\Column(type:"float", nullable: false )]
    private $montant = null;

    #[ORM\Column(type:"boolean", nullable: false)]
    private $paye = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroCommande(): ?int
    {
        return $this->numero_commande;
    }

    public function setNumeroCommande(int $numero_commande): self
    {
        $this->numero_commande = $numero_commande;

        return $this;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->date_commande;
    }

    public function setDateCommande(\DateTimeInterface $date_commande): self
    {
        $this->date_commande = $date_commande;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function isPaye(): ?bool
    {
        return $this->paye;
    }

    public function setPaye(bool $paye): self
    {
        $this->paye = $paye;

        return $this;
    }
}
