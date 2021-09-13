<?php
//------------------------------------------
// Fichier: Cart.php
// Rôle: Classe modèle du panier
// Création: 2021-03-04
// Par: Kevin St-Pierre
//--------------------------------------------

namespace App\Classe;

use App\Entity\Product;

class Cart
{
    private $products = [];

    //----------------------------------------
    // Obtient les produits
    //----------------------------------------
    public function getProducts()
    {
        return $this->products;
    }

    //----------------------------------------
    // Ajoute un produit
    //----------------------------------------
    public function addProduct(Product $p)
    {
        if (count($this->products) != 0) {

            $productIsExisting = false;
            foreach ($this->products as $product)
                if ($product->compareId($p->getId())) {
                    $product->setOrderedQuantity($product->getOrderedQuantity() + 1);
                    $productIsExisting = true;
                    break;
                }

            if (!$productIsExisting)
                $this->products[] = new CartProduct($p);
        } else
            $this->products[] = new CartProduct($p);
    }

    //----------------------------------------
    // Compte les items
    //----------------------------------------
    public function countItems()
    {
        $itemsAmount = 0;
        foreach ($this->products as $product) {
            $itemsAmount += $product->getOrderedQuantity();
        }
        return $itemsAmount;
    }

    //----------------------------------------
    // Retire un produit
    //----------------------------------------
    public function deleteProduct($idProduct)
    {
        $updatedCart = [];
        for ($i = 0; $i < count($this->products); $i++)
            if (!$this->products[$i]->compareId($idProduct))
                $updatedCart[] = $this->products[$i];
        $this->products = $updatedCart;
    }

    //----------------------------------------
    // Calcule le coût total du panier
    //----------------------------------------
    public function calculateAmount()
    {
        $amount = 0;
        if (count($this->products) > 0)
            foreach ($this->products as $product)
                $amount += ($product->getPrice() * $product->getOrderedQuantity());

        return $amount;
    }

    //----------------------------------------
    // Obtient le coût total du panier
    //----------------------------------------
    public function getAmount()
    {
        return number_format((float) $this->calculateAmount(), 2, ',', '');
    }

    //----------------------------------------
    // Calcule les frais de livraison
    //----------------------------------------
    public function calculateShippingCosts()
    {
        if ($this->calculateAmount() < 99)
            return 15;
        else
            return 0;
    }

    //----------------------------------------
    // Obtient les frais de livraison
    //----------------------------------------
    public function getShippingCosts()
    {
        return number_format((float) $this->calculateShippingCosts(), 2, ',', '');
    }

    //----------------------------------------
    // Calcule le sous-total
    //----------------------------------------
    public function calculateSubtotal()
    {
        return $this->calculateAmount() + $this->calculateShippingCosts();
    }

    //----------------------------------------
    // Obtient le sous-total
    //----------------------------------------
    public function getSubtotal()
    {
        return number_format((float) $this->calculateSubtotal(), 2, ',', '');
    }

    //----------------------------------------
    // Calcule la TPS
    //----------------------------------------
    public function calculateTPS()
    {
        return $this->calculateSubtotal() * 5 / 100;
    }

    //----------------------------------------
    // Obtient la TPS
    //----------------------------------------
    public function getTPS()
    {
        return number_format((float) $this->calculateTPS(), 2, ',', '');
    }

    //----------------------------------------
    // Calcule la TVQ
    //----------------------------------------
    public function calculateTVQ()
    {
        return $this->calculateSubtotal() * 9.975 / 100;
    }

    //----------------------------------------
    // Obtient la TVQ
    //----------------------------------------
    public function getTVQ()
    {
        return number_format((float) $this->calculateTVQ(), 2, ',', '');
    }

    //----------------------------------------
    // Calcule le coût total
    //----------------------------------------
    public function calculateTotal()
    {
        return $this->calculateSubtotal() + $this->calculateTPS() + $this->calculateTVQ();
    }

    //----------------------------------------
    // Obtient le coût total
    //----------------------------------------
    public function getTotal()
    {
        return number_format((float) $this->calculateTotal(), 2, ',', '');
    }
}
