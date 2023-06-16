<?php

namespace App\Entity;

use App\Repository\AdresseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdresseRepository::class)]
class Adresse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(min: 1, max: 100)]
    #[Assert\NotBlank()]  // Car ne doit pas être vide (ni null)
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 2, max: 255)]
    #[Assert\NotBlank()]  // Car ne doit pas être vide (ni null)
    private ?string $adresse = null;

    #[ORM\Column(length: 60)]
    #[Assert\Length(min: 2, max: 60)]
    #[Assert\NotBlank()]  // Car ne doit pas être vide (ni null)
    private ?string $ville = null;

    #[ORM\Column(length: 10)]
    #[Assert\Length(min: 3, max: 10)]
    //#[Assert\Type(type:"integer")] // Doit être de type entier
    #[Assert\NotBlank()]  // Car ne doit pas être vide (ni null)
    private ?string $codePostal = null;

    #[ORM\Column(length: 60)]
    #[Assert\Length(min: 2, max: 60)]
    #[Assert\NotBlank()]  // Car ne doit pas être vide (ni null)
    private ?string $pays = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'adresses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

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

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getNom() . " : " . $this->getAdresse() . " " .$this->getcodePostal() . " " . $this->getVille() . " " . $this->getPays();
    }

}
