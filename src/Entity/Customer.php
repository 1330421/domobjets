<?php
//------------------------------------------
// Fichier: Customer.php
// Rôle: Classe modèle d'une entity client
// Création: 2021-04-01
// Par: Kevin St-Pierre
//--------------------------------------------

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 * @UniqueEntity(fields="user", message="Le nom d'utilisateur n'est pas disponible")
 */
class Customer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="user", type="string", length=15, unique=true)
     * @Assert\Length(min=2, minMessage="Le nom d'utilisateur doit avoir deux caractères minimum.")
     * @Assert\Length(max=15, maxMessage="Le nom d'utilisateur doit avoir quinze caractères maximum.")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=15)
     * @Assert\Length(min=2, minMessage="Le prénom doit avoir deux caractères minimum.")
     * @Assert\Length(max=15, maxMessage="Le prénom doit avoir quinze caractères maximum.")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=15)
     * @Assert\Length(min=2, minMessage="Le nom de famille doit avoir deux caractères minimum.")
     * @Assert\Length(max=15, maxMessage="Le nom de famille doit avoir quinze caractères maximum.")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\Regex(pattern="/^(féminin|masculin|neutre)$/i", message="Le genre doit être féminin, masculin ou neutre.")
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=15)
     * @Assert\Length(min=2, minMessage="L'adresse doit avoir deux caractères minimum")
     * @Assert\Length(max=15, maxMessage="L'adresse doit avoir quinze caractères maximum")
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=15)
     * @Assert\Length(min=2, minMessage="La ville doit avoir deux caractères minimum")
     * @Assert\Length(max=15, maxMessage="La ville doit avoir quinze caractères maximum")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=24)
     */
    private $province;

    /**
     * @ORM\Column(type="string", length=6)
     * @Assert\Regex(pattern="/^[a-ceghj-npr-tv-z][0-9][a-ceghj-npr-tv-z]\s?[0-9][a-ceghj-npr-tv-z][0-9]$/i", message="Le code postal doit avoir ce format : A1A 1A1. (lettres D, F, O, Q, I et U interdites)")
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=14)
     * @Assert\Regex(pattern="/^(\([0-9]{3}\)|[0-9]{3})(\-|\s)?[0-9]{3}(\-|\s)?[0-9]{4}$/", message="Le numéro de téléphone doit contenir 10 chiffres.")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/^([a-z]|[0-9]|[\._])+@([a-z]|[0-9]|[\._])+\.[a-z]+$/i", message="Adresse courriel invalide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=2, minMessage="Le mot de passe doit avoir deux caractères minimum")
     * @Assert\Length(max=15, maxMessage="Le mot de passe avoir quinze caractères maximum")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="customer")
     */
    private $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(?string $user): self
    {
        if ($user != null)
            $this->user = $user;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        if ($firstName != null)
            $this->firstName = $firstName;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        if ($name != null)
            $this->name = $name;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        if ($gender != null)
            $this->gender = $gender;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        if ($address != null)
            $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        if ($city != null)
            $this->city = $city;

        return $this;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(?string $province): self
    {
        if ($province != null)
            $this->province = $province;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        if ($postalCode != null)
            $this->postalCode = $postalCode;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        if ($phone != null)
            $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        if ($email != null)
            $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        if ($password != null)
            $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->products->contains($order)) {
            $this->orders[] = $order;
            $order->setCustomer($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getCustomer() === $this) {
                $order->setCustomer(null);
            }
        }

        return $this;
    }
}
