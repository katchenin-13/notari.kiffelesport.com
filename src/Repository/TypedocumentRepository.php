<?php

namespace App\Repository;

use App\Entity\Typedocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Typedocument>
 *
 * @method Typedocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method Typedocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method Typedocument[]    findAll()
 * @method Typedocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypedocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Typedocument::class);
    }

//    /**
//     * @return Typedocument[] Returns an array of Typedocument objects
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

//    public function findOneBySomeField($value): ?Typedocument
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
