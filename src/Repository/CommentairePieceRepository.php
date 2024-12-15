<?php

namespace App\Repository;

use App\Entity\CommentairePiece;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommentairePiece>
 *
 * @method CommentairePiece|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentairePiece|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentairePiece[]    findAll()
 * @method CommentairePiece[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentairePieceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentairePiece::class);
    }

//    /**
//     * @return CommentairePiece[] Returns an array of CommentairePiece objects
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

//    public function findOneBySomeField($value): ?CommentairePiece
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
