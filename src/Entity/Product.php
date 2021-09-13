<?php
//------------------------------------------
// Fichier: Product.php
// Rôle: Classe modèle d'une entité produit
// Création: 2021-04-17
// Par: Kevin St-Pierre
//--------------------------------------------

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ORM\Table(name="`product`")
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $description;

    /**
     * @ORM\Column(type="float")
     * @Assert\Regex(pattern="/^[0-9]*((\,|\.){1}[0-9]{1,2})?$/i", message="Vous devez entrez un prix valide.")
     */
    protected $price;

    /**
     * @ORM\ManyToOne(targetEntity=Brand::class, inversedBy="products", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $brand;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $category;

    /**
     * @ORM\Column(type="integer")
     */
    protected $stockQty;

    /**
     * @ORM\Column(type="integer")
     */
    protected $minThresholdQty;

    /**
     * @ORM\OneToMany(targetEntity=OrderDetail::class, mappedBy="product")
     */
    protected $orderDetails;

    public function __construct(int $id = null, string $name = null, string $description = null, float $price = null, Brand $brand = null, Category $category = null, int $stockQty = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->brand = $brand;
        $this->category = $category;
        $this->stockQty = $stockQty;
        $this->orderDetails = new ArrayCollection();
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getStockQty(): ?int
    {
        return $this->stockQty;
    }

    public function setStockQty(int $stockQty): self
    {
        $this->stockQty = $stockQty;

        return $this;
    }

    public function getMinThresholdQty(): ?int
    {
        return $this->minThresholdQty;
    }

    public function setMinThresholdQty(int $minThresholdQty): self
    {
        $this->minThresholdQty = $minThresholdQty;

        return $this;
    }

    /**
     * @return Collection|OrderDetail[]
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetail $orderDetail): self
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails[] = $orderDetail;
            $orderDetail->setProduct($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetail $orderDetail): self
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getProduct() === $this) {
                $orderDetail->setProduct(null);
            }
        }

        return $this;
    }
}
