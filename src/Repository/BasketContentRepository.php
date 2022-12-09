<?php

namespace App\Repository;

use App\Entity\BasketContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BasketContent>
 *
 * @method BasketContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method BasketContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method BasketContent[]    findAll()
 * @method BasketContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BasketContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BasketContent::class);
    }

    public function save(BasketContent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BasketContent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return BasketContent[] Returns an array of BasketContent objects
//     */
    public function findByBasket($value): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.basket = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
   }

//    public function findOneBySomeField($value): ?BasketContent
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
