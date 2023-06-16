<?php

namespace App\Entity;

use App\Repository\DetailCommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailCommandeRepository::class)]
class DetailCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    #[ORM\Column]
    #[Assert\NotNull()]  // Car ne doit pas être nul
    #[Assert\Positive()]  // Doit être positif
    private ?int $quantite = null;

    #[ORM\Column]
    #[Assert\NotNull()]  // Car ne doit pas être nul
    #[Assert\Positive()]  // Doit être positif
    private ?float $prixTotal = null;

    #[ORM\ManyToOne(inversedBy: 'detailCommandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    public function __construct()
    {
        $this->updatePrixTotal();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;
        $this->updatePrixTotal();

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        $this->updatePrixTotal();

        return $this;
    }

    public function getPrixTotal(): ?float
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(float $prixTotal): self
    {
        $this->prixTotal = $prixTotal;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }

    public function updatePrixTotal(): self
    {
        if ($this->quantite !== null && $this->produit !== null) {
            $this->prixTotal = $this->quantite * $this->produit->getPrix();
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getId() . " : " . $this->getProduit() . " x" . $this->getQuantite();
    }
}
