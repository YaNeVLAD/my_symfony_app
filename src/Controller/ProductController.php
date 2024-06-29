<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\AppConstants;
use App\Service\Data\ProductData;
use App\Service\ProductServiceInterface;
use App\Service\BasketServiceInterface;
use App\Service\UserServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    //Переменные, константы и конструктор класса
    private const NOT_NULLABLE_FORM_FIELDS = ['categorie', 'name', 'price', 'featured'];

    private $userService;

    private $productService;

    private $basketService;

    public function __construct(
        UserServiceInterface $userService,
        ProductServiceInterface $productService,
        BasketServiceInterface $basketService,
    ) {
        $this->userService = $userService;
        $this->productService = $productService;
        $this->basketService = $basketService;
    }
   
    //Публичные методы
    public function showCreateForm(Request $request, string $currCategory): Response
    {
        try {
            $email = $request->getSession()->get(Security::LAST_USERNAME, '');

            if ($this->userService->authorize(UserServiceInterface::ROLE, $email, null)) {
                return $this->render('store/product/create/create_page.html.twig', [
                    'currCategory' => $currCategory,
                    'categories' => AppConstants::EXISTING_CATEGORIES,
                ]);
            }
            throw new \Exception('Access Denied');
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_store', [
                'errorTitle' => 'Update Form Error',
                'errorText' => $e->getMessage(),
            ]);
        }
    }

    public function showUpdateForm(Request $request, int $productId): Response
    {
        try {
            $email = $request->getSession()->get(Security::LAST_USERNAME, '');

            if ($this->userService->authorize(UserServiceInterface::ROLE, $email, null)) {
                $product = $this->productService->find($productId);

                return $this->render('store/product/update/update_page.html.twig', [
                    'product' => $product,
                    'currCategory' => $product->getCategorie(),
                    'categories' => AppConstants::EXISTING_CATEGORIES,
                ]);
            }
            throw new \Exception('Access Denied');
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_store', [
                'errorTitle' => 'Update Form Error',
                'errorText' => $e->getMessage(),
            ]);
        }
    }

    public function createProduct(Request $request): Response
    {
        try {
            $image = $request->files->get('image_path');
            $productData = $this->createFromRequest($request);

            $productId = $this->productService->create($productData, $image);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_store', [
                'errorTitle' => 'Registration Error',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->redirectToRoute('show_product', ['productId' => $productId]);
    }

    public function updateProduct(Request $request): Response
    {
        try {
            $image = $request->files->get('image_path');
            $productData = $this->createFromRequest($request);

            $productId = $this->productService->update($productData, $image);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_store', [
                'errorTitle' => 'Update Error',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->redirectToRoute('show_product', ['productId' => $productId]);
    }

    public function deleteProduct(Request $request, int $productId): Response
    {
        try {
            $email = $request->getSession()->get(Security::LAST_USERNAME, '');

            if ($this->userService->authorize(UserServiceInterface::ROLE, $email, null)) {
                $this->basketService->removeAllByProduct($productId);

                $category = $this->productService->delete($productId);
                if (!$category) {
                    return $this->redirectToRoute('list_product', ['category' => AppConstants::BASE_CATEGORY]);
                }
                return $this->redirectToRoute('list_product', ['category' => $category]);
            }
            throw new \Exception('Access Denied');
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_store', [
                'errorTitle' => 'Delete Error',
                'errorText' => $e->getMessage(),
            ]);
        }
    }

    //Приватные методы
    private function createFromRequest(Request $request): ProductData
    {
        return new ProductData(
            (int) $request->get('productId'),
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
            throw new \Exception('Missing Product`s ' . $formFieldName);
        } elseif ($formFieldValue === '') {
            return null;
        }

        return $formFieldValue;
    }
}