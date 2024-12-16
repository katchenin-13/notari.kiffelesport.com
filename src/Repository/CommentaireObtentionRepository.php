<?php

namespace App\Repository;

use App\Entity\CommentaireObtention;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommentaireObtention>
 *
 * @method CommentaireObtention|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentaireObtention|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentaireObtention[]    findAll()
 * @method CommentaireObtention[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaireObtentionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentaireObtention::class);
    }

//    /**
//     * @return CommentaireObtention[] Returns an array of CommentaireObtention objects
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

//    public function findOneBySomeField($value): ?CommentaireObtention
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
