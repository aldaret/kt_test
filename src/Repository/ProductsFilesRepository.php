<?php

namespace App\Repository;

use App\Entity\ProductsFiles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductsFiles>
 *
 * @method ProductsFiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductsFiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductsFiles[]    findAll()
 * @method ProductsFiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsFilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductsFiles::class);
    }

    public function save(ProductsFiles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProductsFiles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
