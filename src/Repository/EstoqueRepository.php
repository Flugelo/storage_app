<?php

namespace App\Repository;

use App\Entity\Estoque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Estoque>
 *
 * @method Estoque|null find($id, $lockMode = null, $lockVersion = null)
 * @method Estoque|null findOneBy(array $criteria, array $orderBy = null)
 * @method Estoque[]    findAll()
 * @method Estoque[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstoqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Estoque::class);
    }

    public function save(Estoque $entity, bool $flush = false): void
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

    public function search($search)
    {
        $query =  $this->createNativeNamedQuery('
        SELECT produto.name, armazem.name, estoque.unit_price, estoque.quantity, estoque.qtt_max, estoque.qtt_min, estoque.id
        INNER JOIN produto ON produto.id = estoque.produto_id
        INNER JOIN armazem ON armazem.id = estoque.armazem_id
        WHERE produto.name LIKE :search
        AND armazem.name LIKE :search
        AND estoque.unit_price LIKE :search
        ', new ResultSetMapping());

        $query->setParameter('search', '%' . $search . '%');

        return $query->getResult();
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
