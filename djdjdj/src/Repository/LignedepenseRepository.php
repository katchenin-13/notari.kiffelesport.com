<?php

namespace App\Repository;

use App\Entity\Lignedepense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignedepense>
 *
 * @method Lignedepense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignedepense|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignedepense[]    findAll()
 * @method Lignedepense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignedepenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignedepense::class);
    }

//    /**
//     * @return Lignedepense[] Returns an array of Lignedepense objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Lignedepense
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
