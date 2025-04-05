<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Repository\ProductRepository;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use function response;

class ProductController extends Controller
{

    public const ITEMS_PER_PAGE = 2;

    /**
     * @var ProductRepository
     */
    private ProductRepository $productRepository;

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getProducts(Request $request): mixed
    {
        $queryParams = $request->all();

        $itemsPerPage = $queryParams['itemsPerPage'] ?? self::ITEMS_PER_PAGE;

        unset($queryParams['page']);
        unset($queryParams['itemsPerPage']);

        $products = $this->productRepository->getProducts($queryParams, $itemsPerPage ?? self::ITEMS_PER_PAGE);

        return response()->json($products, Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function getProductItem(string $id): mixed
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['data' => ['error' => 'Not found product by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => $product], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function createProduct(Request $request): mixed
    {
        $requestData = json_decode($request->getContent(), true);

        $category = Category::find($requestData['category']);

        if (!$category) {
            return response()->json(['data' => ['error' => 'Not found category by id ' . $requestData['category']]], Response::HTTP_NOT_FOUND);
        }

        $product = $category->products()->create([
            'name'        => $requestData['name'],
            'price'       => $requestData['price'],
            'description' => $requestData['description']
        ]);

        return response()->json([
            'data' => $product
        ], Response::HTTP_CREATED);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function deleteProduct(string $id): mixed
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['data' => ['error' => 'Not found product by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        $product->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $id
     * @param Request $request
     * @return mixed
     */
    public function updateProduct(string $id, Request $request): mixed
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['data' => ['error' => 'Not found product by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        $requestData = json_decode($request->getContent(), true);

        $product->update([
            'price' => $requestData['price']
        ]);

        return response()->json([
            'data' => $product
        ], Response::HTTP_CREATED);
    }

}
