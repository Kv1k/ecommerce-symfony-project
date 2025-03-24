<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'cards')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'cards')]
    private $products;

    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: Quantity::class)]
    private $quantities;

    #[ORM\Column(type: 'array')]
    private $quantity = [];

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $this->quantity[$product->getId()] = 1;
            $this->setQuantity($this->quantity);

        } else {
            $this->quantity[$product->getId()]++;
            $this->setQuantity($this->quantity);
        }
        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if (isset($this->quantity[$product->getId()])) {
            if ($this->quantity[$product->getId()] - 1 < 1) {
            unset($this->quantity[$product->getId()]);
            $this->products->removeElement($product);
            } else {
                $this->quantity[$product->getId()]--;
            }
            $this->setQuantity($this->quantity);
        }
        return $this;
    }

    public function eraseProduct(Product $product):self
    {
        if ($this->products->contains($product)) {
            unset($this->quantity[$product->getId()]);
            $this->products->removeElement($product);
        }
        return $this;
    }

    public function getQuantity(): ?array
    {
        return $this->quantity;
    }

    public function setQuantity(array $quantity): self
    {
        $this->quanity = $quantity;

        return $this;
    }
}
