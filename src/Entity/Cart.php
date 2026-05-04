<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'cart', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    /**
     * @var Collection<int, Add>
     */
    #[ORM\OneToMany(targetEntity: Add::class, mappedBy: 'cart', orphanRemoval: true)]
    private Collection $adds;

    public function __construct()
    {
        $this->adds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Add>
     */
    public function getAdds(): Collection
    {
        return $this->adds;
    }

    public function addAdd(Add $add): static
    {
        if (!$this->adds->contains($add)) {
            $this->adds->add($add);
            $add->setCart($this);
        }

        return $this;
    }

    public function removeAdd(Add $add): static
    {
        if ($this->adds->removeElement($add)) {
            // set the owning side to null (unless already changed)
            if ($add->getCart() === $this) {
                $add->setCart(null);
            }
        }

        return $this;
    }
}
