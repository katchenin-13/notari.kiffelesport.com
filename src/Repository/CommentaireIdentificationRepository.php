<?php

namespace App\Repository;

use App\Entity\CommentaireIdentification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommentaireIdentification>
 *
 * @method CommentaireIdentification|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentaireIdentification|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentaireIdentification[]    findAll()
 * @method CommentaireIdentification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaireIdentificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentaireIdentification::class);
    }

//    /**
//     * @return CommentaireIdentification[] Returns an array of CommentaireIdentification objects
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

//    public function findOneBySomeField($value): ?CommentaireIdentification
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
