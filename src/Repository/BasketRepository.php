<?php

namespace App\Repository;

use App\Entity\Basket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Basket>
 *
 * @method Basket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Basket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Basket[]    findAll()
 * @method Basket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BasketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Basket::class);
    }

    public function save(Basket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Basket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Basket[] Returns an array of Basket objects
//     */
   public function findByUtilisateur($value): array
   {
       return $this->createQueryBuilder('b')
           ->andWhere('b.owner = :user')
           ->setParameter('user', $value)
           ->andWhere('b.state = :val')
           ->setParameter('val', false)
           ->orderBy('b.id', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }

   public function findPaidBasketByUtilisateur($value): array
   {
       return $this->createQueryBuilder('b')
           ->andWhere('b.owner = :user')
           ->setParameter('user', $value)
           ->andWhere('b.state = :val')
           ->setParameter('val', 1)
           ->orderBy('b.buyDate', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }

   public function findOneByUtilisateur($value): ?Basket
   {
       return $this->createQueryBuilder('b')
       ->andWhere('b.owner = :user')
       ->setParameter('user', $value)
       ->andWhere('b.state = :val')
       ->setParameter('val', false)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

   public function findById($value): ?Basket
   {
    return $this->createQueryBuilder('b')
       ->andWhere('b.id = :id')
       ->setParameter('id', $value)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }
}
