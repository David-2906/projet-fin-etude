<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private $id;

    #[ORM\Column(type: "string", length: 180, unique: true, nullable: false, options:["collation" =>"utf8mb4_general_ci"])]
    private $email;

    #[ORM\Column(type:"json")]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type:"string", length:60, nullable: false, options:["collation" => "utf8mb4_general_ci"])]
    private $password;

    #[ORM\Column(type:"string", length: 255, nullable: false, options:["collation" => "utf8mb4_general_ci"])]
    private $nom = null;

    #[ORM\Column(type:"string", length: 255, nullable: false, options:["collation" => "utf8mb4_general_ci"])]
    private $prenom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private $date_naissance = null;

    #[ORM\Column(type:"string", length: 255, nullable: false, options:["collation" => "utf8mb4_general_ci"])]
    private $adresse = null;

    #[ORM\Column(type:"string", length: 255, nullable: true, options:["collation" => "utf8mb4_general_ci"])]
    private $complement_adresse = null;

    #[ORM\Column(type:"string", length: 255, nullable: false, options:["collation" => "utf8mb4_general_ci"])]
    private $ville = null;

    #[ORM\Column(type:"string", length: 255, nullable: false, options:["collation" => "utf8mb4_general_ci"])]
    private $code_postal = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private $date_enregistrement = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Panier $panier = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Commande::class, orphanRemoval: true)]
    private Collection $commande;

    #[ORM\OneToMany(targetEntity: ResetPasswordRequest::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private $resetpassword;
    
    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Order::class)]
    private Collection $orders;

    public function __construct()
    {
        $this->commande = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }


    public function __toString(): string
    {
        return $this->adresse . '[-br]' .
           $this->code_postal . '[-br]' .
           $this->ville . '[-br]';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): self
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getComplementAdresse(): ?string
    {
        return $this->complement_adresse;
    }

    public function setComplementAdresse(?string $complement_adresse): self
    {
        $this->complement_adresse = $complement_adresse;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(string $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getDateEnregistrement(): ?\DateTimeInterface
    {
        return $this->date_enregistrement;
    }

    public function setDateEnregistrement(\DateTimeInterface $date_enregistrement): self
    {
        $this->date_enregistrement = $date_enregistrement;

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): self
    {
        // unset the owning side of the relation if necessary
        if ($panier === null && $this->panier !== null) {
            $this->panier->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($panier !== null && $panier->getUser() !== $this) {
            $panier->setUser($this);
        }

        $this->panier = $panier;

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommande(): Collection
    {
        return $this->commande;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commande->contains($commande)) {
            $this->commande->add($commande);
            $commande->setUser($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commande->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUser() === $this) {
                $commande->setUser(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }
}
