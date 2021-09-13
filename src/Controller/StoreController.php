<?php
//------------------------------------------
// Fichier: StoreControlleur.php
// Rôle: Contrôleur initial du projet
// Création: 2021-02-20
// Par: Kevin St-Pierre
//--------------------------------------------

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StoreController extends AbstractController
{
    /**
     * @Route("/", name="catalog") 
     */
    public function store(Request $request)
    {
        $filter = $this->getFilter($request);

        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository(Product::class)
            ->findAllByFilter($filter);

        if (
            $filter['search'] != null ||
            $filter['idCategory'] != null ||
            $filter['idBrand'] != null
        ) $this->addFlash('notice', count($products) . (count($products) > 1 ? ' produits trouvés' : ' produit trouvé'));

        $brands = $em->getRepository(Brand::class)
            ->findAll();

        $categories = $em->getRepository(Category::class)
            ->findAll();

        return $this->render("catalog.html.twig", [
            'products' => $products,
            'brands' => $brands,
            'categories' => $categories,
            'filter' => $filter,
            'cart' => $this->getCart($request),
            'user' => $request->getSession()->get('connectedUser')
        ]);
    }

    /**
     * @Route("/addProduct", name="addProduct") 
     */
    public function addProduct(Request $request)
    {
        $idProduct = $request->query->get('idProduct');

        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($idProduct);

        $cart = $this->addToCart($request, $product);

        $request->getSession()->set('cart', $cart);

        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        $brands = $this->getDoctrine()
            ->getRepository(Brand::class)
            ->findAll();

        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render("catalog.html.twig", [
            'products' => $products,
            'brands' => $brands,
            'categories' => $categories,
            'filter' => $this->getFilter($request),
            'cart' => $this->getCart($request),
            'user' => $request->getSession()->get('connectedUser')
        ]);
    }

    /**
     * @Route("/clearSession", name="clarSession")
     */
    public function clearSession(Request $request)
    {
        $request->getSession()->remove('cart');
        $request->getSession()->remove('customer');
        $request->getSession()->set('cart', new Cart());

        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        $brands = $this->getDoctrine()
            ->getRepository(Brand::class)
            ->findAll();

        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render("catalog.html.twig", [
            'products' => $products,
            'brands' => $brands,
            'categories' => $categories,
            'filter' => $this->getFilter($request),
            'cart' => $this->getCart($request),
            'user' => $request->getSession()->get('connectedUser')
        ]);
    }

    //----------------------------------------
    // Obtient les filtres de la requête
    //----------------------------------------
    public function getFilter(Request $request)
    {
        $filter = [
            'idCategory' => $request->query->get('idCategory'),
            'idBrand' => $request->query->get('idBrand'),
            'search' => $request->query->get('search')
        ];

        if (strlen($request->request->get('search')))
            $filter['search'] = $request->request->get('search');

        return $filter;
    }

    //----------------------------------------
    // Obtient le panier de la session
    //----------------------------------------
    public function getCart(Request $request)
    {
        $cart = $request->getSession()->get('cart');
        if ($cart == null) {
            $cart = new Cart();
            $request->getSession()->set('cart', $cart);
        }
        return $cart;
    }

    //----------------------------------------
    // Ajoute le produit au panier
    //----------------------------------------
    public function addToCart($request, $product)
    {
        $cart = $request->getSession()->get('cart');
        if ($cart == null)
            $cart = new Cart();
        $cart->addProduct($product);
        return $cart;
    }
}
