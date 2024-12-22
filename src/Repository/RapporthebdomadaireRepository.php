<?php

namespace App\Repository;

use App\Entity\Rapporthebdomadaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rapporthebdomadaire>
 *
 * @method Rapporthebdomadaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rapporthebdomadaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rapporthebdomadaire[]    findAll()
 * @method Rapporthebdomadaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RapporthebdomadaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rapporthebdomadaire::class);
    }

//    /**
//     * @return Rapporthebdomadaire[] Returns an array of Rapporthebdomadaire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Rapporthebdomadaire
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
