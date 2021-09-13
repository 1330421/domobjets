<?php
//------------------------------------------
// Fichier: CartProduct.php
// Rôle: Classe modèle d'un produit dans le panier
// Création: 2021-03-04
// Par: Kevin St-Pierre
//--------------------------------------------

namespace App\Classe;

use App\Entity\Product;

class CartProduct extends Product
{
    private $orderedQuantity = 1;

    function __construct(Product $p)
    {
        parent::__construct($p->id, $p->name, $p->description, $p->price, $p->brand, $p->category, $p->stockQty);
    }

    //----------------------------------------
    // Obtient la quantité à commander
    //----------------------------------------
    public function getOrderedQuantity()
    {
        return $this->orderedQuantity;
    }

    //----------------------------------------
    // Fixe la quantité à commander
    //----------------------------------------
    public function setOrderedQuantity($quantity)
    {
        $this->orderedQuantity = $quantity;
    }

    //----------------------------------------
    // Compare l'identifiant en paramètre à
    // l'identifiant de l'objet courant
    //----------------------------------------
    public function compareId($idProduct)
    {
        return $idProduct == $this->id;
    }
}
