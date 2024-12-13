<?php

namespace App\Repository;

use App\Entity\Comptefour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Compte>
 *
 * @method Comptefour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comptefour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comptefour[]    findAll()
 * @method Comptefour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComptefourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comptefour::class);
    }

//    /**
//     * @return Compte[] Returns an array of Compte objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Compte
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
