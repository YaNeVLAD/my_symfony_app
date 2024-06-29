<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\AppConstants;
use App\Service\UserServiceInterface;
use App\Service\OrderServiceInterface;
use App\Service\BasketServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StoreFrontController extends AbstractController
{
    //Переменные, константы и конструктор класса
    private $userService;

    private $orderService;

    private $basketService;

    public function __construct(
        UserServiceInterface $userService,
        OrderServiceInterface $orderService,
        BasketServiceInterface $basketService
    ) {
        $this->userService = $userService;
        $this->orderService = $orderService;
        $this->basketService = $basketService;
    }

    //Публичные методы
    public function index(): Response
    {
        return $this->redirectToRoute('list_order', [
            'category' => AppConstants::BASE_CATEGORY,
        ]);
    }

    public function showThankYouPage(Request $request): Response
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME, '');
        $this->basketService->removeAllByUser($this->userService->getUserByEmail($email)->getId());

        return $this->render('store/thank_you/thank_you_page.html.twig');
    }

    public function showUpdateForm(): Response
    {
        return $this->render('store/order/update/update_page.html.twig', [
            'categories' => AppConstants::EXISTING_CATEGORIES,
        ]);
    }

    public function errorPage(Request $request): Response
    {
        return $this->render('custom_error/error_page.html.twig', [
            'errorTitle' => $request->get('errorTitle'),
            'errorText' => $request->get('errorText'),
        ]);
    }

    public function listByCategory(Request $request): Response
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME, '');
        $role = $this->userService->getUserByEmail($email)->getRole();

        $category = $request->get('category');

        if (!array_key_exists($category, AppConstants::EXISTING_CATEGORIES)) {
            return $this->redirectToRoute('error_store', [
                'errorTitle' => 'Wrong Category Error',
                'errorText' => 'This category doesn\'t exist',
            ]);
        }
        $orders = $this->orderService->findAllInCategory($category);

        return $this->render(
            'store/order/list/list_page.html.twig',
            [
                'role' => $role,
                'category' => $category,
                'orders' => $orders,
                'categories' => AppConstants::EXISTING_CATEGORIES,
            ]
        );
    }

    public function showOrder(Request $request, int $orderId): Response
    {
        try {
            $email = $request->getSession()->get(Security::LAST_USERNAME, '');
            $role = $this->userService->getUserByEmail($email)->getRole();
            $order = $this->orderService->find($orderId);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_store', [
                'errorTitle' => 'Show Order Error',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->render('store/order/view/view_page.html.twig', [
            'order' => $order, 
            'role' => $role,
            'categories' => AppConstants::EXISTING_CATEGORIES
        ]);
    }

    public function addToBasket(Request $request, int $orderId): Response
    {
        try {
            $email = $request->getSession()->get(Security::LAST_USERNAME, '');

            $user = $this->userService->getUserByEmail($email);
            $order = $this->orderService->find($orderId);

            $this->basketService->add($user, $order);
        } catch (\Exception $e) {
            return $this->redirectToRoute('login_user');
        }

        return $this->redirectToRoute('list_order', [
            'category' => $order->getCategorie()
        ]);
    }

    public function showBasket(Request $request): Response
    {
        try {
            $email = $request->getSession()->get(Security::LAST_USERNAME, '');

            $user = $this->userService->getUserByEmail($email);

            $basketItems = $this->basketService->show($user->getId());
        } catch (\Exception $e) {
            return $this->redirectToRoute('register_user_form');
        }

        return $this->render('store/basket/basket_page.html.twig', [
            'basketItems' => $basketItems,
        ]);
    }

    public function increaseBasketItemCounter(Request $request, int $orderId): Response
    {
        try {
            $email = $request->getSession()->get(Security::LAST_USERNAME, '');

            $user = $this->userService->getUserByEmail($email);

            $order = $this->orderService->find($orderId);

            $this->basketService->increaseCount($user, $order);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_store', [
                'errorTitle' => 'Increase Basket Counter Error',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->redirectToRoute('basket_order_form');
    }

    public function decreaseBasketItemCounter(Request $request, int $orderId): Response
    {
        try {
            $email = $request->getSession()->get(Security::LAST_USERNAME, '');

            $user = $this->userService->getUserByEmail($email);

            $order = $this->orderService->find($orderId);

            $this->basketService->decreaseCount($user, $order);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_store', [
                'errorTitle' => 'Decrease Basket Counter Error',
                'errorText' => $e->getMessage(),
            ]);
        }
        return $this->redirectToRoute('basket_order_form');
    }

    public function removeFromBasket(int $itemId): Response
    {
        try {
            $this->basketService->remove($itemId);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_store', [
                'errorTitle' => 'Failed to delete basket item',
                'errorText' => $e->getMessage(),
            ]);
        }
        return $this->redirectToRoute('basket_order_form');
    }
}
