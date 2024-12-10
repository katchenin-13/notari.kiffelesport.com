<?php

namespace App\Repository\Courrier;

use App\Entity\Courrier\CourierArrive;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CourierArrive>
 *
 * @method CourierArrive|null find($id, $lockMode = null, $lockVersion = null)
 * @method CourierArrive|null findOneBy(array $criteria, array $orderBy = null)
 * @method CourierArrive[]    findAll()
 * @method CourierArrive[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourierArriveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourierArrive::class);
    }

//    /**
//     * @return CourierArrive[] Returns an array of CourierArrive objects
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

//    public function findOneBySomeField($value): ?CourierArrive
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
