<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;  // Pour API (Nécessaire pour les filtres et recherches)
use ApiPlatform\Metadata\ApiResource;  // Pour API
use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;  // Pour API (pour types de filtre)
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;  // Pour API (pour types de recherche)

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;  // Pour API (pour les groupes)
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;  // Nécessaire pour l'import des images
use Vich\UploaderBundle\Mapping\Annotation as Vich;  // Nécessaire pour l'import des images

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ORM\HasLifecycleCallbacks]  // Nécessaire pour la mettre à jour la date de mofification "setUpdatedAtValue()"
#[Vich\Uploadable]  // Nécessaire pour l'import des images
#[ApiResource(
    normalizationContext: [ "groups" => ["read:product"]]
)]
#[ApiFilter(OrderFilter::class, properties: ['id' => 'DESC', 'nom' => 'ASC'])]  // Pour filtrer les id par ordre décroissant et noms par ordre croissant
#[ApiFilter(SearchFilter::class, properties: ['id' => 'partial', 'price' => 'exact',  'nom' => 'ipartial', 'description' => 'ipartial'])]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["read:product"])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank()]  // Car ne doit pas être vide (ni null)
    #[Assert\Length(min: 2, max: 100)]
    #[Groups(["read:product"])]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank()]  // Car ne doit pas être vide (ni null)
    #[Groups(["read:product"])]
    private ?string $description = null;

    #[Vich\UploadableField(mapping: 'produit_images', fileNameProperty: 'imageName')]  // A paramétrer en fonction du ficher config/packages/vich_uploader.yaml
    #[Groups(["read:product"])]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["read:product"])]
    private ?string $imageName = null;

    #[ORM\Column]
    #[Assert\NotNull()]  // Car ne doit pas être nul
    #[Assert\Positive()]  // Doit être positif
    #[Assert\LessThan(1000)]  // Doit être inférieur à 1000
    #[Groups(["read:product"])]
    private ?float $prix = null;

    #[ORM\Column]
    #[Assert\NotNull()]  // Car ne doit pas être nul
    #[Assert\Positive()]  // Doit être positif
    #[Groups(["read:product"])]
    private ?int $stock = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull()]  // Car ne doit pas être nul
    #[Groups(["read:product"])]
    private ?SousCategorie $sousCategorie = null;

    #[ORM\Column]
    #[Assert\NotNull()]  // Ne doit pas être nul
    #[Groups(["read:product"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Assert\NotNull()]  // Ne doit pas être nul
    #[Groups(["read:product"])]
    private ?\DateTimeImmutable $updatedAt = null;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

   /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }


    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getSousCategorie(): ?SousCategorie
    {
        return $this->sousCategorie;
    }

    public function setSousCategorie(?SousCategorie $sousCategorie): self
    {
        $this->sousCategorie = $sousCategorie;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist()]
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->getId() . " | " . $this->getNom();
    }

}
