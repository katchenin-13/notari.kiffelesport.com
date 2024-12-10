<?php

namespace App\Repository;

use App\Entity\Typedepense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Typedepense>
 *
 * @method Typedepense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Typedepense|null findOneBy(array $criteria, array $orderBy = null)
 * @method Typedepense[]    findAll()
 * @method Typedepense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypedepenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Typedepense::class);
    }

//    /**
//     * @return Typedepense[] Returns an array of Typedepense objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Typedepense
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
