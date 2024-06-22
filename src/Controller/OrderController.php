<?php
declare(strict_types=1);

namespace App\Controller;

use App\Constants\AppConstants;
use App\Service\Data\OrderData;
use App\Service\OrderServiceInterface;
use App\Service\BasketServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    //Переменные, константы и конструктор класса
    private const NOT_NULLABLE_FORM_FIELDS = ['categorie', 'name', 'price', 'featured'];

    private $orderService;

    private $basketService;

    public function __construct(
        OrderServiceInterface $orderService,
        BasketServiceInterface $basketService,
    ) {
        $this->orderService = $orderService;
        $this->basketService = $basketService;
    }

    public function showCreateForm(string $currCategory): Response
    {
        return $this->render('store/order/create/create_page.html.twig', [
            'currCategory' => $currCategory,
            'categories' => AppConstants::EXISTING_CATEGORIES,
        ]);
    }

    //Публичные методы
    public function showUpdateForm(int $orderId): Response
    {
        try {
            $order = $this->orderService->find($orderId);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_store', [
                'errorTitle' => 'Update Form Error',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->render('store/order/update/update_page.html.twig', [
            'order' => $order,
            'currCategory' => $order->getCategorie(),
            'categories' => AppConstants::EXISTING_CATEGORIES,
        ]);
    }

    public function createOrder(Request $request): Response
    {
        try {
            $image = $request->files->get('image_path');
            $orderData = $this->createFromRequest($request);

            $orderId = $this->orderService->create($orderData, $image);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_store', [
                'errorTitle' => 'Registration Error',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->redirectToRoute('show_order', ['orderId' => $orderId]);
    }

    public function updateOrder(Request $request): Response
    {
        try {
            $image = $request->files->get('image_path');
            $orderData = $this->createFromRequest($request);

            $orderId = $this->orderService->update($orderData, $image);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_store', [
                'errorTitle' => 'Update Error',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->redirectToRoute('show_order', ['orderId' => $orderId]);
    }

    public function deleteOrder(int $orderId): Response
    {
        try {
            $this->basketService->removeAllByOrder($orderId);
            
            $category = $this->orderService->delete($orderId);
            if (!$category) {
                return $this->redirectToRoute('list_order', ['category' => AppConstants::BASE_CATEGORY]);
            }
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_store', [
                'errorTitle' => 'Delete Error',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->redirectToRoute('list_order', ['category' => $category]);
    }

    //Приватные методы
    private function createFromRequest(Request $request): OrderData
    {
        return new OrderData(
            (int) $request->get('orderId'),
            $this->validateCategory($request->get('categorie')),
            $this->validateData($request, 'name'),
            $this->validateData($request, 'description'),
            null,
            (int) $this->validateData($request, 'price'),
            (int) $this->validateData($request, 'featured'),
        );
    }

    private function validateCategory(string $formFieldValue): ?string
    {
        return array_key_exists($formFieldValue, AppConstants::EXISTING_CATEGORIES)
            ? $formFieldValue
            : throw new \Exception('This category doesn\'t exist');
    }

    private function validateData(Request $request, string $formFieldName): ?string
    {
        $formFieldValue = $request->get($formFieldName);

        if (in_array($formFieldName, self::NOT_NULLABLE_FORM_FIELDS) and $formFieldValue === '') {
            throw new \Exception('Missing Order`s ' . $formFieldName);
        } elseif ($formFieldValue === '') {
            return null;
        }

        return $formFieldValue;
    }
}