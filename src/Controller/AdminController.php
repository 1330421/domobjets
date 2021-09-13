<?php
//------------------------------------------
// Fichier: adminController.php
// Rôle: Contrôleur de l'administration
// Création: 2021-05-05
// Par: Kevin St-Pierre
//--------------------------------------------

namespace App\Controller;

use App\Classe\ProductImages;
use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\BrandType;
use App\Form\CategoryType;
use App\Form\ProductImagesType;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function connection(Request $request): Response
    {
        // Valide que l'admin est connecté
        $admin = $request->getSession()->get('connectedAdmin');
        if ($admin == 'admin') {
            return $this->redirectToRoute('administer');
        }

        if (isset($_POST['admin'])) {
            $user = $_POST['admin'];

            if ($user == 'admin') {
                $customer = $this->getDoctrine()
                    ->getRepository(Customer::class)
                    ->findOneBy([
                        'user' => $user
                    ]);

                if ($customer != null && $customer != null && password_verify($_POST['password'], $customer->getPassword())) {
                    $request->getSession()->set('connectedAdmin', $customer->getUser());
                    $this->addFlash('admin', 'Bienvenue ' . $customer->getUser());

                    return $this->redirectToRoute('administer'); //TODO
                }

                $this->addFlash('admin-danger', 'Le mot de passe est incorrect.');
            } else
                $this->addFlash('admin-danger', 'Le nom d\'utilisateur est incorrect.');
        }

        return $this->render('admin/connection.html.twig', []);
    }

    /**
     * @Route("/administer", name="administer")
     */
    public function administer(Request $request): Response
    {
        $request->getSession()->remove('idProductToModify');

        // Valide que l'admin est connecté
        $admin = $request->getSession()->get('connectedAdmin');
        if ($admin != 'admin') {
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/menu.html.twig', [
            'admin' => $admin
        ]);
    }

    /**
     * @Route("/add-product", name="add-product")
     */
    public function addProduct(Request $request): Response
    {
        // Valide que l'admin est connecté
        $admin = $request->getSession()->get('connectedAdmin');
        if ($admin != 'admin') {
            return $this->redirectToRoute('admin');
        }

        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        $product = new Product();
        $form = $this->get('form.factory')->create(ProductType::class, $product);

        // Modifibation et ajout d'un produit
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('admin-success', 'Le produit a été ajouté avec succès.');

            return $this->redirectToRoute('administer');
        }

        return $this->render('admin/modificationProducts.html.twig', [
            'admin' => $admin,
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit-products", name="edit-products")
     */
    public function editProducts(Request $request): Response
    {
        // Valide que l'admin est connecté
        $admin = $request->getSession()->get('connectedAdmin');
        if ($admin != 'admin') {
            return $this->redirectToRoute('admin');
        }

        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        $idProduct = $request->getSession()->get('idProductToModify');
        if ($idProduct == null)
            return $this->redirectToRoute('add-product');

        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($idProduct);

        $form = $this->get('form.factory')->create(ProductType::class, $product);
        // Modification et ajout d'un produit
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('admin-success', 'Le produit a été modifié avec succès.');

            return $this->redirectToRoute('administer');
        }

        $productImages = new ProductImages();
        $productImages->setProduct($product);
        $imagesForm = $this->get('form.factory')->createNamed('productImages', ProductImagesType::class, $productImages);
        //Modification des images d'un produit
        $imagesForm->handleRequest($request);
        if ($imagesForm->isSubmitted() && $imagesForm->isValid()) {

            $errorCode = 0;
            if ($productImages->getProductImage() != null && $productImages->uploadProductImage($errorCode)) {
                $this->addFlash('admin-success', 'L\'image du produit a été téléversée avec succès.');
            } else {
                $this->addFlash('admin-danger', 'L\'image de la description m\'a été téléversée.');
            }

            if ($productImages->getDescriptionImage() != null && $productImages->uploadDescriptionImage($errorCode)) {
                $this->addFlash('admin-success', 'L\'image de la description a été téléversée avec succès.');
            } else {
                $this->addFlash('admin-danger', 'L\'image de la description n\'a été téléversée.');
            }

            return $this->redirectToRoute('administer');
        }

        return $this->render('admin/modificationProducts.html.twig', [
            'admin' => $admin,
            'isEditing' => true,
            'products' => $products,
            'form' => $form->createView(),
            'imagesForm' => $imagesForm->createView()
        ]);
    }

    /**
     * @Route("/edit-products/{idProduct}")
     */
    public function editProduct(Request $request, int $idProduct): Response
    {
        $request->getSession()->set('idProductToModify', $idProduct);

        return $this->redirectToRoute('edit-products');
    }

    /**
     * @Route("/edit-categories", name="edit-categories")
     */
    public function editcategories(Request $request): Response
    {
        // Valide que l'admin est connecté
        $admin = $request->getSession()->get('connectedAdmin');
        if ($admin != 'admin')
            return $this->redirectToRoute('admin');

        // Récupère les catégories
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        // Crée le formulaire d'ajout d'une catégorie
        $category = new Category();
        $form = $this->get('form.factory')->create(CategoryType::class, $category);

        // Tente d'ajouter une catégorie
        if ($this->addCategory($request, $form, $category)) {

            $this->addFlash('admin-success', 'La catégorie a été ajoutée avec succès.');
            return $this->redirectToRoute('administer');
        }
        // Tente de mettre à jour les catégories
        else if ($this->updateCategories($request, $categories)) {

            $this->addFlash('admin-success', 'Les catégories ont été mises à jour avec succès.');
            return $this->redirectToRoute('administer');
        }

        return $this->render('admin/modificationCategories.html.twig', [
            'admin' => $admin,
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit-brands", name="edit-brands")
     */
    public function editBrands(Request $request): Response
    {
        // Valide que l'admin est connecté
        $admin = $request->getSession()->get('connectedAdmin');
        if ($admin != 'admin')
            return $this->redirectToRoute('admin');

        // Récupère les marques
        $brands = $this->getDoctrine()
            ->getRepository(Brand::class)
            ->findAll();

        // Crée le formulaire d'ajout d'une marque
        $brand = new Brand();
        $form = $this->get('form.factory')->create(BrandType::class, $brand);

        // Tente d'ajouter une marque
        if ($this->addBrand($request, $form, $brand)) {

            $this->addFlash('admin-success', 'La marque a été ajoutée avec succès.');
            return $this->redirectToRoute('administer');
        }
        // Tente de mettre à jour les marques
        else if ($this->updateBrands($request, $brands)) {

            $this->addFlash('admin-success', 'Les marques ont été mises à jour avec succès.');
            return $this->redirectToRoute('administer');
        }

        return $this->render('admin/modificationBrands.html.twig', [
            'admin' => $admin,
            'brands' => $brands,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/show-inventory", name="show-inventory")
     */
    public function showInventory(Request $request): Response
    {
        // Valide que l'admin est connecté
        $admin = $request->getSession()->get('connectedAdmin');
        if ($admin != 'admin') {
            return $this->redirectToRoute('admin');
        }

        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findNotToOrder();

        $productsToOrder =  $this->getDoctrine()
            ->getRepository(Product::class)
            ->findToOrder();

        return $this->render('admin/inventory.html.twig', [
            'admin' => $admin,
            'products' => $products,
            'productsToOrder' => $productsToOrder
        ]);
    }

    /**
     * @Route("/show-sales-report", name="show-sales-report")
     */
    public function showSalesReport(Request $request): Response
    {
        // Valide que l'admin est connecté
        $admin = $request->getSession()->get('connectedAdmin');
        if ($admin != 'admin') {
            return $this->redirectToRoute('admin');
        }

        $orders = $this->getDoctrine()
            ->getRepository(Order::class)
            ->findAll();

        return $this->render('admin/salesReport.html.twig', [
            'admin' => $admin,
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/sign-out", name="sign-out")
     */
    public function signOut(Request $request): Response
    {
        $request->getSession()->remove('connectedAdmin');
        $this->addFlash('admin', 'Au revoir');
        return $this->redirectToRoute('admin');
    }

    //----------------------------------------
    // Tente d'ajouter une catégorie
    //----------------------------------------
    public function addCategory(Request $request, $form, Category $category)
    {
        $form->handleRequest($request);

        // Vérifie si le formulaire d'ajout a été envoyé
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return true;
        }

        return false;
    }

    //----------------------------------------
    // Tente de mettre à jour les catégories
    //----------------------------------------
    public function updateCategories(Request $request, $categories)
    {
        if ($request->isMethod('post')) {

            foreach ($categories as $c) {
                $categoryName = $_POST['c' . $c->getId()];
                if ($categoryName != $c->getname()) {
                    $c->setName($categoryName);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->flush();
                }
            }

            return true;
        }

        return false;
    }

    //----------------------------------------
    // Tente d'ajouter une marque
    //----------------------------------------
    public function addBrand(Request $request, $form, Brand $brand)
    {
        $form->handleRequest($request);

        // Vérifie si le formulaire d'ajout a été envoyé
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($brand);
            $entityManager->flush();

            return true;
        }

        return false;
    }

    //----------------------------------------
    // Tente de mettre à jour les marques
    //----------------------------------------
    public function updateBrands(Request $request, $brands)
    {
        if ($request->isMethod('post')) {

            foreach ($brands as $b) {
                $brandName = $_POST['b' . $b->getId()];
                if ($brandName != $b->getname()) {
                    $b->setName($brandName);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->flush();
                }
            }

            return true;
        }

        return false;
    }
}
