<?php

namespace App\Repository;

use App\Entity\FichierAccusseReception;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FichierAccusseReception>
 *
 * @method FichierAccusseReception|null find($id, $lockMode = null, $lockVersion = null)
 * @method FichierAccusseReception|null findOneBy(array $criteria, array $orderBy = null)
 * @method FichierAccusseReception[]    findAll()
 * @method FichierAccusseReception[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FichierAccusseReceptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FichierAccusseReception::class);
    }

//    /**
//     * @return FichierAccusseReception[] Returns an array of FichierAccusseReception objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FichierAccusseReception
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
