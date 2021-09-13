<?php
//------------------------------------------
// Fichier: CartControlleur.php
// Rôle: Contrôleur du panier
// Création: 2021-03-02
// Par: Kevin St-Pierre
//--------------------------------------------

namespace App\Controller;

use App\Classe\Cart;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart") 
     */
    public function Cart(Request $request)
    {
        $cart = $request->getSession()->get('cart');
        if ($cart == null)
            $cart = new Cart();

        if (count($cart->getProducts()) == 0)
            $this->addFlash('notice-danger', 'Le panier est vide.');

        return $this->getRender($request, $cart);
    }

    /**
     * @Route("/deleteProduct", name="deleteProduct") 
     */
    public function deleteProduct(Request $request)
    {
        $cart = $request->getSession()->get('cart');
        $idProduct = $request->query->get('idProduct');
        $cart->deleteProduct($idProduct);
        $request->getSession()->set('cart', $cart);

        return $this->getRender($request, $cart);
    }

    /**
     * @Route("/refresh", name="refresh")
     */
    public function refresh(Request $request)
    {
        $cart = $request->getSession()->get('cart');

        foreach ($cart->getProducts() as $product)
            if (isset($_POST['orderedQuantity' . $product->getId()])) {
                $quantity = $_POST['orderedQuantity' . $product->getId()];
                if ($quantity > 0)
                    $product->setOrderedQuantity($quantity);
                else
                    $cart->deleteProduct($product->getId());
            }

        $request->getSession()->set('cart', $cart);
        return $this->getRender($request, $cart);
    }

    /**
     * @Route("/clear", name="clear")
     */
    public function clear(Request $request)
    {
        $request->getSession()->remove('cart');
        $request->getSession()->set('cart', new Cart());
        $cart = $request->getSession()->get('cart');
        return $this->render("cart.html.twig", [
            'cart' => $cart,
            'user' => $request->getSession()->get('connectedUser')
        ]);
    }

    //----------------------------------------
    // Obtient le render pricipal de la page
    //----------------------------------------
    public function getRender(Request $request, $cart)
    {
        return $this->render("cart.html.twig", [
            'cart' => $cart,
            'user' => $request->getSession()->get('connectedUser')
        ]);
    }

    /**
     * @Route("/showSession", name="showSession") 
     */
    public function showSession(Request $request)
    {
        $cart = $request->getSession()->get('cart');

        return $this->render("voirSession.html.twig", [
            'cart' => $cart,
            'user' => $request->getSession()->get('connectedUser')
        ]);
    }
}
