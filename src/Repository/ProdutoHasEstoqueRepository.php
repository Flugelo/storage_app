<?php

namespace App\Repository;

use App\Entity\ProdutoHasEstoque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProdutoHasEstoque>
 *
 * @method ProdutoHasEstoque|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProdutoHasEstoque|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProdutoHasEstoque[]    findAll()
 * @method ProdutoHasEstoque[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProdutoHasEstoqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProdutoHasEstoque::class);
    }

    public function save(ProdutoHasEstoque $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProdutoHasEstoque $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ProdutoHasEstoque[] Returns an array of ProdutoHasEstoque objects
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

//    public function findOneBySomeField($value): ?ProdutoHasEstoque
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
