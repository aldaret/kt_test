<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function getProductsPaginator(array $params = null)
    {
        $query = $this->createQueryBuilder('c');

        if ($params['weight'] == 'light') {
            $query = $query
                ->orderBy('c.weight', 'ASC');
        }elseif ($params['weight'] == 'hight') {
            $query = $query
                ->orderBy('c.weight', 'DESC');
        }

        if (null !== $params['category']) {
            $query = $query
                ->where('c.category = :value')
                ->setParameter('value', $params['category']);
        }

        return $query->getQuery();
    }

    public function getCategories(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.category AS name')
            ->groupBy('c.category')
            ->getQuery()
            ->getResult();
    }
}
