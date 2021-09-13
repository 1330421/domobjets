<?php
//------------------------------------------
// Fichier: ProductImages.php
// Rôle: Classe modèle des images d'un produit
// Création: 2021-05-05
// Par: Kevin St-Pierre
//--------------------------------------------

namespace App\Classe;

use App\Entity\Product;
use Monolog\Handler\IFTTTHandler;
use Symfony\Component\HttpFoundation\File\UploadedFile;

const FOLDER_NAME = __DIR__ . '/../../public/images/';

class ProductImages
{
    private $product;
    private $productImage;
    private $descriptionImage;

    //----------------------------------------
    // Obtient le produit
    //----------------------------------------
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    //----------------------------------------
    // Fixe le produit
    //----------------------------------------
    public function setProduct(?Product $product)
    {
        $this->product = $product;
    }

    //----------------------------------------
    // Obtient l'image du produit
    //----------------------------------------
    public function getProductImage(): ?UploadedFile
    {
        return $this->productImage;
    }

    //----------------------------------------
    // Fixe l'image du produit
    //----------------------------------------
    public function setProductImage(UploadedFile $fichier = null)
    {
        $this->productImage = $fichier;
    }

    //----------------------------------------
    // Obtient l'image de la description 
    //----------------------------------------
    public function getDescriptionImage(): ?UploadedFile
    {
        return $this->descriptionImage;
    }

    //----------------------------------------
    // Fixe l'image de la description
    //----------------------------------------
    public function setDescriptionImage(UploadedFile $fichier = null)
    {
        $this->descriptionImage = $fichier;
    }

    //----------------------------------------
    // Téléverse l'image du produit
    //----------------------------------------
    public function uploadProductImage(&$codeErreur)
    {
        if ($this->productImage->isValid()) {
            $type = $this->productImage->getClientMimeType();
            if (
                $type == 'image/jpeg' ||
                $type == 'image/png' ||
                $type == 'image/gif'
            ) {
                $fileName = $this->product->getId() . '.jpg';
                $this->productImage->move(FOLDER_NAME, $fileName);
                return true;
            } else {
                $codeErreur = -1;
                return false;
            }
        } else {
            $codeErreur = $this->productImage->getError();
            return false;
        }
    }

    //----------------------------------------
    // Téléverse l'image de la description
    //----------------------------------------
    public function uploadDescriptionImage(&$codeErreur)
    {
        if ($this->descriptionImage->isValid()) {
            $type = $this->descriptionImage->getClientMimeType();
            if (
                $type == 'image/jpeg' ||
                $type == 'image/png' ||
                $type == 'image/gif'
            ) {
                $fileName = $this->product->getId() . '-2.jpg';
                $this->descriptionImage->move(FOLDER_NAME, $fileName);
                return true;
            } else {
                $codeErreur = -1;
                return false;
            }
        } else {
            $codeErreur = $this->descriptionImage->getError();
            return false;
        }
    }
}
