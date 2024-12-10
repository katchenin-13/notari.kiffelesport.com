<?php

namespace App\Repository;

use App\Entity\Paimentmarche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Paimentmarche>
 *
 * @method Paimentmarche|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paimentmarche|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paimentmarche[]    findAll()
 * @method Paimentmarche[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaimentmarcheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paimentmarche::class);
    }

//    /**
//     * @return Paimentmarche[] Returns an array of Paimentmarche objects
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

//    public function findOneBySomeField($value): ?Paimentmarche
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
