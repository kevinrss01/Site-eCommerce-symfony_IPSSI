<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: ProductsRepository::class)]
class Products
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Your first name must be at least {{ limit }} characters long',
        maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    )]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Your first name must be at least {{ limit }} characters long',
        maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    )]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\Positive]
    private ?float $prix = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private ?int $stock = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\OneToMany(mappedBy: 'products', targetEntity: BasketContent::class, orphanRemoval: true)]
    private Collection $basketContent;

    public function __construct()
    {
        $this->basketContent = new ArrayCollection();
    }


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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
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

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }


    public function __toString()
    {
        return $this->name;
    }

    public function getBasketContent(): ?BasketContent
    {
        return $this->basketContent;
    }

    public function setBasketContent(BasketContent $basketContent): self
    {
        // set the owning side of the relation if necessary
        if ($basketContent->getProducts() !== $this) {
            $basketContent->setProducts($this);
        }

        $this->basketContent = $basketContent;

        return $this;
    }

    public function addBasketContent(BasketContent $basketContent): self
    {
        if (!$this->basketContent->contains($basketContent)) {
            $this->basketContent->add($basketContent);
            $basketContent->setProducts($this);
        }

        return $this;
    }

    public function removeBasketContent(BasketContent $basketContent): self
    {
        if ($this->basketContent->removeElement($basketContent)) {
            // set the owning side to null (unless already changed)
            if ($basketContent->getProducts() === $this) {
                $basketContent->setProducts(null);
            }
        }

        return $this;
    }
}
