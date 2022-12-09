<?php

namespace App\Entity;

use App\Repository\BasketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BasketRepository::class)]
class Basket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'baskets')]
    private ?User $owner = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE,  nullable: true)]
    private ?\DateTimeInterface $buyDate = null;

    #[ORM\Column]
    private ?bool $state = null;

    #[ORM\OneToMany(mappedBy: 'basket', targetEntity: BasketContent::class, orphanRemoval: true)]
    private Collection $basketContents;

    public function __construct(){
        $this->state = false;
        $this->basketContents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getBuyDate(): ?\DateTimeInterface
    {
        return $this->buyDate;
    }

    public function setBuyDate(\DateTimeInterface $buyDate): self
    {
        $this->buyDate = $buyDate;

        return $this;
    }

    public function isState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, BasketContent>
     */
    public function getBasketContents(): Collection
    {
        return $this->basketContents;
    }

    public function addBasketContent(BasketContent $basketContent): self
    {
        if (!$this->basketContents->contains($basketContent)) {
            $this->basketContents->add($basketContent);
            $basketContent->setBasket($this);
        }

        return $this;
    }

    public function removeBasketContent(BasketContent $basketContent): self
    {
        if ($this->basketContents->removeElement($basketContent)) {
            // set the owning side to null (unless already changed)
            if ($basketContent->getBasket() === $this) {
                $basketContent->setBasket(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->owner;
    }
}
