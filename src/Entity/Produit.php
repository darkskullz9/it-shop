<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The name cannot be empty.')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'Minimum 2 characters.', maxMessage: 'Maximum 255 characters.')]
    private ?string $designation = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    #[Assert\NotBlank(message: 'The price cannot be empty.')]
    #[Assert\Positive(message: 'The price must be a positive number.')]
    private ?float $prix = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'produits')]
    private Collection $favoris;

    #[ORM\Column(length: 500)]
    #[Assert\NotBlank(message: 'The description cannot be empty.')]
    #[Assert\Length(min: 5, max: 500, minMessage: 'Minimum 5 characters.', maxMessage: 'Maximum 500 characters.')]
    private ?string $description;

    /**
     * @var Collection<int, Add>
     */
    #[ORM\OneToMany(targetEntity: Add::class, mappedBy: 'product', orphanRemoval: true)]
    private Collection $adds;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'produit')]
    private Collection $orderItems;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    private ?Category $category = null;

    public function __construct()
    {
        $this->favoris = new ArrayCollection();
        $this->adds = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): static
    {
        $this->designation = $designation;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(User $favori): static
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris->add($favori);
        }

        return $this;
    }

    public function removeFavori(User $favori): static
    {
        $this->favoris->removeElement($favori);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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
            $add->setProduct($this);
        }

        return $this;
    }

    public function removeAdd(Add $add): static
    {
        if ($this->adds->removeElement($add)) {
            // set the owning side to null (unless already changed)
            if ($add->getProduct() === $this) {
                $add->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setProduit($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getProduit() === $this) {
                $orderItem->setProduit(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
