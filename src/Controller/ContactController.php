<?php
//------------------------------------------
// Fichier: ContactControlleur.php
// Rôle: Contrôleur de la page Contact
// Création: 2021-02-20
// Par: Kevin St-Pierre
//--------------------------------------------

namespace App\Controller;

use App\Classe\Cart;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact") 
     */
    public function Contact(Request $request)
    {
        if ($request->getSession()->get('cart') == null)
            $cart = new Cart();
        else
            $cart = $request->getSession()->get('cart');

        return $this->render("contact.html.twig", [
            'cart' => $cart,
            'user' => $request->getSession()->get('connectedUser')
        ]);
    }
}
