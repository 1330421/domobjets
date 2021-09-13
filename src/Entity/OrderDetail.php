<?php
//------------------------------------------
// Fichier: OrderDetail.php
// Rôle: Classe modèle d'une entité détail d'une commande
// Création: 2021-04-17
// Par: Kevin St-Pierre
//--------------------------------------------

namespace App\Entity;

use App\Classe\CartProduct;
use App\Repository\OrderDetailRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderDetailRepository::class)
 */
class OrderDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantityOut = 0;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="orderDetails", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="orderDetails", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    public function __construct(Product $product, int $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
        if ($this->quantity > $this->product->getStockQty()) {
            $this->quantityOut = $this->quantity - $product->getStockQty();
            $this->product->setStockQty(0);
        } else {
            $this->product->setStockQty($this->product->getStockQty() - $this->quantity);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuantityOut(): ?int
    {
        return $this->quantityOut;
    }

    public function setQuantityOut(int $quantityOut): self
    {
        $this->quantityOut = $quantityOut;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}
