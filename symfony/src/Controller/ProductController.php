<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1')]
final class ProductController extends AbstractController
{

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    /**
     * @return JsonResponse
     */
    #[Route('/products', name: 'get_products', methods: [Request::METHOD_GET])]
    public function getProducts(): JsonResponse
    {
        /** @var Product $product */
        $products = $this->entityManager->getRepository(Product::class)->findAll();

        return new JsonResponse(['data' => $products], Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/products/{id}', name: 'get_product_item', methods: [Request::METHOD_GET])]
    public function getProductItem(string $id): JsonResponse
    {
        /** @var Product $product */
        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return new JsonResponse(['data' => ['error' => 'Not found product by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['data' => $product], Response::HTTP_OK);
    }

    #[Route('/products', name: 'post_products', methods: [Request::METHOD_POST])]
    public function createProduct(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        /** @var Category $category */
        $category = $this->entityManager->getRepository(Category::class)->find($requestData['category']);

        if (!$category) {
            return new JsonResponse(['data' => ['error' => 'Not found category by id ' . $requestData['category']]], Response::HTTP_NOT_FOUND);
        }

        $product = new Product();

        $product->setName($requestData['name'])
            ->setDescription($requestData['description'])
            ->setPrice($requestData['price'])
            ->setCategory($category);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return new JsonResponse([
            'data' => $product
        ], Response::HTTP_CREATED);
    }

    #[Route('/products/{id}', name: 'patch_products', methods: [Request::METHOD_PATCH])]
    public function updateProduct(string $id, Request $request): JsonResponse
    {
        /** @var Product $product */
        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return new JsonResponse(['data' => ['error' => 'Not found product by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        $requestData = json_decode($request->getContent(), true);

        $product->setPrice($requestData['price']);

        $this->entityManager->flush();

        return new JsonResponse([
            'data' => $product
        ], Response::HTTP_OK);
    }

    #[Route('/products/{id}', name: 'delete_products', methods: [Request::METHOD_DELETE])]
    public function deleteProduct(string $id): JsonResponse
    {
        /** @var Product $product */
        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return new JsonResponse(['data' => ['error' => 'Not found product by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

}
