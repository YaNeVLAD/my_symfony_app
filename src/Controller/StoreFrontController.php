<?php
declare(strict_types=1);

//СУЩНОСТЬ Order изменить на сущность Product???
//Создать новую сущность Order
//У неё будет primary key order_id
//внешний ключ user_id от User
//внешний ключ product_id от Product
//поле order_adress varchar(255) not null
//поле order_date datetime (DateTimeImmutable)
//При нажатии кнопки оплатить со страницы basket
//Происходит поиск всех заказов по текущему id пользователя
//Создание сущностей Order(null, User $user, Order $order, string $adress, DateTimeImmutable date())
//Сделать возможность смотреть и удалять эти заказы с отдельных страничек

namespace App\Controller;

use App\Constants\AppConstants;
use App\Service\Data\BasketData;
use App\Service\UserServiceInterface;
use App\Service\OrderServiceInterface;
use App\Service\BasketServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    public function index(SessionInterface $session): Response
    {
        $session->set(AppConstants::USER_SESSION_NAME, AppConstants::UNAUTHORIZED_ID);

        return $this->redirectToRoute('list_order', [
            'category' => AppConstants::BASE_CATEGORY,
        ]);
    }

    public function showPaymentForm(): Response
    {
        return $this->render('store/payment/payment_page.html.twig');
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

    public function listByCategory(Request $request, SessionInterface $session): Response
    {
        $category = $request->get('category');
        $userId = $session->get(AppConstants::USER_SESSION_NAME, AppConstants::UNAUTHORIZED_ID);

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
                'userId' => $userId,
                'category' => $category,
                'orders' => $orders,
                'categories' => AppConstants::EXISTING_CATEGORIES,
            ]
        );
    }

    public function showOrder(int $orderId): Response
    {
        try {
            $order = $this->orderService->find($orderId);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_store', [
                'errorTitle' => 'Show Order Error',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->render('store/order/view/view_page.html.twig', ['order' => $order]);
    }

    public function addToBasket(int $orderId, SessionInterface $session): Response
    {
        try {
            $user = $this->userService->getUser($session->get(AppConstants::USER_SESSION_NAME, AppConstants::UNAUTHORIZED_ID));
            $order = $this->orderService->find($orderId);

            $this->basketService->add($user, $order);

        } catch (\Exception $e) {
            return $this->redirectToRoute('register_user_form');
        }

        return $this->redirectToRoute('list_order', [
            'category' => $order->getCategorie()
        ]);
    }

    public function showBasket(SessionInterface $session): Response
    {
        try {
            $userId = $session->get(AppConstants::USER_SESSION_NAME, AppConstants::UNAUTHORIZED_ID);
            $basketItems = $this->basketService->show($userId);
        } catch (\Exception $e) {
            return $this->redirectToRoute('register_user_form');
        }

        return $this->render('store/basket/basket_page.html.twig', [
            'basketItems' => $basketItems,
        ]);
    }

    public function increaseBasketItemCounter(int $orderId, SessionInterface $session): Response
    {
        try {
            $user = $this->userService->getUser($session->get(AppConstants::USER_SESSION_NAME, AppConstants::UNAUTHORIZED_ID));
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

    public function decreaseBasketItemCounter(int $orderId, SessionInterface $session): Response
    {
        try {
            $user = $this->userService->getUser($session->get(AppConstants::USER_SESSION_NAME, AppConstants::UNAUTHORIZED_ID));
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

    public function removeFromBasket(int $itemId, SessionInterface $session): Response
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