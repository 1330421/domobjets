<?php
//------------------------------------------
// Fichier: OrderControlleur.php
// Rôle: Contrôleur des page de gestion de commandes
// Création: 2021-04-17
// Par: Kevin St-Pierre
//--------------------------------------------

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\CartProduct;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Entity\Product;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/placeOrder", name="placeOrder")
     */
    public function placeOrder(Request $request): Response
    {
        if ($request->getSession()->get('connectedUser') == null) {
            $this->addFlash('notice-danger', 'Vous devez vous connecter avant de passer la commande.');
            $request->getSession()->set('isOrdering', true);
            return $this->redirectToRoute('log-in');
        }

        return $this->render('order/orderConfirmation.html.twig', [
            'cart' => $request->getSession()->get('cart'),
            'user' => $request->getSession()->get('connectedUser')
        ]);
    }

    /**
     * @Route("/pay", name="pay")
     */
    public function pay(Request $request): Response
    {
        // Numéro Visa valide 4510156018359542

        if ($request->isMethod('post')) {
            return $this->redirectToRoute('order');
        }

        return $this->render('order/payment.html.twig', [
            'cart' => $request->getSession()->get('cart'),
            'user' => $request->getSession()->get('connectedUser'),
        ]);
    }

    /**
     * @Route("/order", name="order")
     */
    public function order(Request $request): Response
    {
        $user = $request->getSession()->get('connectedUser');
        $cart = $request->getSession()->get('cart');

        $repository = $this->getDoctrine()->getRepository(Customer::class);
        $customer = $repository->findOneBy(['user' => $user]);

        if (count($cart->getProducts()) != 0) {
            $order = $this->createOrder($customer, $cart);
            $this->addFlash('notice-success', 'Commande confirmée');
            $cart = new Cart();
            $request->getSession()->set('cart', $cart);
        } else {
            $repository = $this->getDoctrine()->getRepository(Order::class);
            $order = $repository->findMostRecent($customer);
        }

        return $this->render('order/confirmedOrder.html.twig', [
            'cart' => $cart,
            'user' => $user,
            'order' => $order
        ]);
    }

    /**
     * @Route("/showOrders", name="showOrders")
     */
    public function showOrders(Request $request): Response
    {
        $user = $request->getSession()->get('connectedUser');

        $customer = $this->getDoctrine()
            ->getRepository(Customer::class)
            ->findOneBy(['user' => $user]);

        $orders = $this->getDoctrine()
            ->getRepository(Order::class)
            ->findBy(
                ['customer' => $customer],
                ['orderDate' => 'DESC']
            );

        if (count($orders) == 0)
            $this->addFlash('notice-danger', 'Vous n\'avez passée aucune commade.');

        $timers = $this->getTimers($orders);

        return $this->render('order/orders.html.twig', [
            'cart' => $this->getCart($request),
            'user' => $user,
            'orders' => $orders,
            'timers' => $timers,
            'isToRefresh' => true
        ]);
    }

    /**
     * @Route("/cancelOrder/{idOrder}", name="cancelOrder")
     */
    public function cancelOrder(Request $request, int $idOrder): Response
    {
        if (!$this->validateUser($request, $idOrder)) {
            return $this->redirectToRoute('catalog');
        }

        return $this->render('order/confirmationDeletion.html.twig', [
            'cart' => $request->getSession()->get('cart'),
            'user' => $request->getSession()->get('connectedUser'),
            'idOrder' => $idOrder
        ]);
    }

    /**
     * @Route("/confirmDeletion/{idOrder}")
     */
    public function confirmDeletion(Request $request, int $idOrder): Response
    {
        if (!$this->validateUser($request, $idOrder)) {
            return $this->redirectToRoute('catalog');
        }

        $order = $this->getDoctrine()
            ->getRepository(Order::class)
            ->find($idOrder);
        $idOrder = $order->getId();

        $this->deleteOrder($order);
        $this->addFlash('notice-danger', 'La commande ' . $idOrder . ' a été annulée avec succès.');

        return $this->redirectToRoute('showOrders');
    }

    //----------------------------------------
    // Crée une nouvelle commande dans la base de données
    //----------------------------------------
    private function createOrder(Customer $customer, Cart $cart)
    {
        $order = new Order($customer);

        foreach ($cart->getProducts() as $cartProduct) {
            $product =  $this->getDoctrine()->getRepository(Product::class)->find($cartProduct->getId());
            $order->addOrderDetail(new  OrderDetail($product, $cartProduct->getOrderedQuantity()));
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($order);
        $entityManager->flush();
        return $order;
    }

    //----------------------------------------
    // Supprime une commande dans la base de données
    //----------------------------------------
    private function deleteOrder(Order $order)
    {
        foreach ($order->getOrderDetails() as $orderDetail) {
            $product = $orderDetail->getProduct();
            $product->setStockQty($product->getStockQty() + $orderDetail->getQuantity() - $orderDetail->getQuantityOut());
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($order);
        $entityManager->flush();
    }

    //----------------------------------------
    // Obtient les minuteur du temps restant
    // pour annuler les commandes
    //----------------------------------------
    private function getTimers(array $orders): array
    {
        define('MINUTE', 60);
        define('HOUR', 60 * MINUTE);
        define('DAY', 24 * HOUR);

        $timers = [];
        foreach ($orders as $order) {
            $elapsedTime = (new DateTime)->getTimestamp() - $order->getOrderDate()->getTimestamp();
            $remainingTime = 2 * DAY - $elapsedTime;

            $numberDays = intval($remainingTime / DAY);
            $numberHours = intval(($remainingTime - ($numberDays * DAY)) / HOUR);
            $numberMinutes = intval(($remainingTime - ($numberDays * DAY) - ($numberHours * HOUR)) / MINUTE);
            $numberSeconds = intval(($remainingTime - ($numberDays * DAY) - ($numberHours * HOUR) - ($numberMinutes * MINUTE)));
            if ($remainingTime <= 0) {
                $timers[$order->getId()] = 0;
            } else if ($remainingTime < MINUTE)
                $timers[$order->getId()] = "$numberSeconds seconde" . ($numberSeconds > 1 ? 's' : '');
            else if ($remainingTime < HOUR)
                $timers[$order->getId()] = "$numberMinutes minute" . ($numberMinutes > 1 ? 's' : '') . " $numberSeconds seconde" . ($numberSeconds > 1 ? 's' : '');
            else if ($remainingTime < DAY)
                $timers[$order->getId()] = "$numberHours heure" . ($numberHours > 1 ? 's' : '') . ($numberMinutes != 0 ? " $numberMinutes minute" . ($numberMinutes > 1 ? 's' : '') : '');
            else
                $timers[$order->getId()] = "$numberDays jour" . ($numberHours != 0 ? " $numberHours heure" . ($numberHours > 1 ? 's' : '') : '');
        }
        return $timers;
    }

    //----------------------------------------
    // Valide que la commande appartient à
    // l'utilisateur connecté
    //----------------------------------------
    private function validateUser(Request $request, int $idOrder): bool
    {
        $order = $this->getDoctrine()
            ->getRepository(Order::class)
            ->find($idOrder);

        return $order->getCustomer()->getUser() == $request->getSession()->get('connectedUser');
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
}
