<?php
//------------------------------------------
// Fichier: CustomerControlleur.php
// Rôle: Contrôleur des pages de gestion de client
// Création: 2021-02-01
// Par: Kevin St-Pierre
//--------------------------------------------

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Customer;
use App\Entity\Product;
use App\Form\CustomerType;
use Monolog\Handler\IFTTTHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    /**
     * @Route("/create-account", name="create-account")
     */
    public function createAccount(Request $request): Response
    {
        $customer = $request->getSession()->get('candidateCustomer');
        if ($customer == null) {
            $customer = new Customer();
        }

        $form = $this->get('form.factory')->create(CustomerType::class, $customer);

        // Si le formulaire est soumis
        if ($request->isMethod('post')) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $customer->setPostalCode(strToUpper(str_replace(' ', '', $customer->getPostalCode())));

                $request->getSession()->set('candidateCustomer', $customer);

                return $this->render('customer/confirmAccount.html.twig', [
                    'candidateCustomer' => $customer,
                    'cart' => $this->getCart($request)
                ]);
            }
        }

        return $this->render('customer/createAccount.html.twig', [
            'form' => $form->createView(),
            'cart' => $this->getCart($request)
        ]);
    }

    /**
     * @Route("/confirm-account", name="confirm")
     */
    public function confirmAccount(Request $request)
    {
        $customer = $request->getSession()->get('candidateCustomer');
        $customer->setPassword(password_hash($customer->getPassword(), PASSWORD_DEFAULT));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($customer);
        $entityManager->flush();

        $user = $customer->getUser();
        $request->getSession()->remove('candidateCustomer');
        $request->getSession()->set('connectedUser', $user);

        $this->addFlash('notice', "Bienvenue $user");

        return $this->redirectToRoute('catalog');
    }

    /**
     * @Route("/edit-account", name="edit-account")
     */
    public function editAccount(Request $request): Response
    {
        $user = $request->getSession()->get('connectedUser');
        $customer = $this->getDoctrine()
            ->getRepository(Customer::class)
            ->findOneBy(['user' => $user]);
        $currentPassword = $customer->getPassword();

        $form = $this->get('form.factory')->create(CustomerType::class, $customer);
        $passwordForm = $this->get('form.factory')->createNamed('customerPassword', CustomerType::class, $customer);

        // Si le formulaire est soumis
        if ($request->isMethod('post')) {

            // Modification du compte
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $customer = $entityManager->getRepository(Customer::class)->find($customer->getId());
                $entityManager->flush();
                $this->addFlash('notice-success', 'Le compte a été modifié.');
                return $this->redirectToRoute('catalog');
            }

            // Modification du mot de passe
            $passwordForm->handleRequest($request);
            if ($passwordForm->isSubmitted() && $passwordForm->isValid())
                if (password_verify($_POST['currentPassword'], $currentPassword)) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $customer->setPassword(password_hash($customer->getPassword(), PASSWORD_DEFAULT));
                    $entityManager->flush();
                    $this->addFlash('notice-success', 'Le mot de passe a été modifié.');
                    return $this->redirectToRoute('catalog');
                } else
                    $this->addFlash('passwordError', 'Le mot de passe est incorrect.');
        }

        return $this->render('customer/editAccount.html.twig', [
            'form' => $form->createView(),
            'passwordForm' => $passwordForm->createView(),
            'cart' => $this->getCart($request),
            'user' => $user
        ]);
    }

    /**
     * @Route("/log-in", name="log-in")
     */
    public function connection(Request $request): Response
    {
        if (isset($_POST['user'])) {

            $customer = $this->getDoctrine()
                ->getRepository(Customer::class)
                ->findOneBy([
                    'user' => $_POST['user']
                ]);

            if ($customer != null && password_verify($_POST['password'], $customer->getPassword())) {
                $request->getSession()->set('connectedUser', $customer->getUser());
                $this->addFlash('notice', 'Bienvenue ' . $customer->getUser());

                if ($request->getSession()->get('isOrdering') != null) {
                    $request->getSession()->remove('isOrdering');
                    return $this->redirectToRoute('placeOrder');
                }

                return $this->redirectToRoute('catalog');
            }

            $this->addFlash('notice-danger', 'Le nom d\'utilisateur ou le mot de passe est incorrect.');
        }

        return $this->render('customer/connection.html.twig', [
            'cart' => $this->getCart($request)
        ]);
    }

    /**
     * @Route("/log-out", name="log-out")
     */
    public function deconnection(Request $request): Response
    {
        $request->getSession()->remove('connectedUser');
        $this->addFlash('notice', 'Au revoir');

        return $this->redirectToRoute('catalog');
    }

    //----------------------------------------
    // Obtient le panier de la session
    //----------------------------------------
    private function getCart(Request $request)
    {
        $cart = $request->getSession()->get('cart');
        if ($cart == null)
            $cart = new Cart();
        return $cart;
    }

    //----------------------------------------
    // Récupère le client avec son nom d'utilisateur et son mot de passe
    //----------------------------------------
    private function retrieveCustomerByUser($user, $password)
    {
        $repository = $this->getDoctrine()->getRepository(Customer::class);
        return $repository->findOneBy([
            'user' => $user,
            'password' => $password
        ]);
    }
}
