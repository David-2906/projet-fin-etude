<?php

namespace App\Entity;

use App\Repository\FaqRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FaqRepository::class)]
class Faq
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private $id = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false, options:["collation" => "utf8mb4_general_ci"])]
    private $questions = null;

    #[ORM\Column(type: Types::TEXT , length: 255, nullable: false, options:["collation" => "utf8mb4_general_ci"])]
    private $reponses = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestions(): ?string
    {
        return $this->questions;
    }

    public function setQuestions(string $questions): self
    {
        $this->questions = $questions;

        return $this;
    }

    public function getReponses(): ?string
    {
        return $this->reponses;
    }

    public function setReponses(string $reponses): self
    {
        $this->reponses = $reponses;

        return $this;
    }
}
