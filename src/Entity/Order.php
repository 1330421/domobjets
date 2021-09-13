<?php
//------------------------------------------
// Fichier: Order.php
// Rôle: Classe modèle d'une entité commande
// Création: 2021-04-17
// Par: Kevin St-Pierre
//--------------------------------------------

namespace App\Entity;

use App\Repository\OrderRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\Column(type="datetime")
     */
    private $orderDate;

    /**
     * @ORM\OneToMany(targetEntity=OrderDetail::class, mappedBy="order", cascade={"persist", "remove"})
     */
    private $orderDetails;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
        $this->orderDate = new DateTime();
        $this->orderDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): self
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

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
            $orderDetail->setOrder($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetail $orderDetail): self
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getOrder() === $this) {
                $orderDetail->setOrder(null);
            }
        }

        return $this;
    }

    //----------------------------------------
    // Calcule le total
    //----------------------------------------
    public function calculateTotal()
    {
        $total = 0;
        foreach ($this->orderDetails as $orderDetail)
            $total += $orderDetail->getProduct()->getPrice() * $orderDetail->getQuantity();

        if ($total < 99)
            $total += 15;

        $total += $total * 5 / 100;
        $total += $total * 9.975 / 100;

        return $total;
    }
}
