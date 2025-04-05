<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param array $params
     * @param int $page
     * @param int $itemsPerPage
     * @return array
     */
    #[ArrayShape([
        'projects'       => "mixed",
        'totalPageCount' => "float",
        'totalItems'     => "int"
    ])] public function getProducts(array $params = [], int $page = 1, int $itemsPerPage = 1): array
    {
        $queryBuilder = $this->createQueryBuilder('product')
            ->join('product.category', 'category');

        $queryBuilder = $this->mapParams($queryBuilder, $params);

        //        if (isset($params['name'])) {
        //            $queryBuilder
        //                ->andWhere('product.name LIKE :name')
        //                ->setParameter('name', '%' . $params['name'] . '%');
        //        }
        //
        //        if (isset($params['price']['gte'])) {
        //            $queryBuilder
        //                ->andWhere('product.price >= :priceGte')
        //                ->setParameter('priceGte', $params['price']['gte']);
        //        }
        //
        //        if (isset($params['price']['lte'])) {
        //            $queryBuilder
        //                ->andWhere('product.price <= :priceLte')
        //                ->setParameter('priceLte', $params['price']['lte']);
        //        }
        //
        //        if (isset($params['description'])) {
        //            $queryBuilder
        //                ->andWhere('product.description LIKE :description')
        //                ->setParameter('description', '%' . $params['description'] . '%');
        //        }

        $paginator = new Paginator ($queryBuilder);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $itemsPerPage);

        $paginator
            ->getQuery()
            ->setFirstResult($itemsPerPage * ((int)$page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'projects'       => $paginator->getQuery()->getResult(),
            'totalPageCount' => $pagesCount,
            'totalItems'     => $totalItems
        ];
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $params
     * @return QueryBuilder
     */
    private function mapParams(QueryBuilder $queryBuilder, array $params): QueryBuilder
    {
        foreach ($params as $key => $value) {

            $ourKey = $key;
            $ourValue = $value;

            if (is_array($value)) {
                $ourKey = $key . ucfirst(array_key_first($value));
                $ourValue = $value[array_key_first($value)];
            }

            $queryBuilder
                ->andWhere('product.' . $key . ' LIKE :' . $ourKey)
                ->setParameter($ourKey, '%' . $ourValue . '%');
        }

        return $queryBuilder;
    }

    //    /**
    //     * @param array $filterData
    //     * @param string $itemsPerPage
    //     * @param string $page
    //     * @return array
    //     */
    //    public function getProjects(
    //        array  $filterData,
    //        string $itemsPerPage,
    //        string $page
    //    ): array
    //    {
    //        $queryBuilder = $this->createQueryBuilder('project');
    //
    //        if (isset($filterData["name"])) {
    //            $queryBuilder->andWhere('project.name LIKE :name')
    //                ->setParameter('name', '%' . $filterData["name"] . '%');
    //        }
    //
    //        $paginator = new Paginator ($queryBuilder);
    //        $totalItems = count($paginator);
    //        $pagesCount = ceil($totalItems / $itemsPerPage);
    //
    //        $paginator
    //            ->getQuery()
    //            ->setFirstResult($itemsPerPage * ((int)$page - 1))
    //            ->setMaxResults($itemsPerPage);
    //
    //        return [
    //            'projects'       => $paginator->getQuery()->getResult(),
    //            'totalPageCount' => $pagesCount,
    //            'totalItems'     => $totalItems
    //        ];
    //    }

}
