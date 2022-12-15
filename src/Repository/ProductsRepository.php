<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Products>
 *
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    public const PRODUCTS_PER_PAGE = 6;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    public function save(Products $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Products $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getProductsPaginator(Request $request, PaginatorInterface $paginator)
    {
        $query = $this->createQueryBuilder('c');

        if ($request->query->get('weight') == 'light') {
            $query = $query
                ->orderBy('c.weight', 'ASC');
        }elseif ($request->query->get('weight') == 'hight') {
            $query = $query
                ->orderBy('c.weight', 'DESC');
        }

        if (null !== $request->query->get('category')) {
            $query = $query
                ->where('c.category = :value')
                ->setParameter('value', $request->query->get('category'));
        }

        $query = $query->getQuery();

        return $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::PRODUCTS_PER_PAGE
        );
    }

    public function getCategories(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.category AS name')
            ->groupBy('c.category')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Products[] Returns an array of Products objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Products
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
