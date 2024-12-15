<?php

namespace App\Repository;

use App\Entity\CommentaireRedaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommentaireRedaction>
 *
 * @method CommentaireRedaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentaireRedaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentaireRedaction[]    findAll()
 * @method CommentaireRedaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaireRedactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentaireRedaction::class);
    }

//    /**
//     * @return CommentaireRedaction[] Returns an array of CommentaireRedaction objects
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

//    public function findOneBySomeField($value): ?CommentaireRedaction
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
