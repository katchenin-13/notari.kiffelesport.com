<?php

namespace App\Repository;

use App\Entity\CommentaireRemise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommentaireRemise>
 *
 * @method CommentaireRemise|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentaireRemise|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentaireRemise[]    findAll()
 * @method CommentaireRemise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaireRemiseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentaireRemise::class);
    }

//    /**
//     * @return CommentaireRemise[] Returns an array of CommentaireRemise objects
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

//    public function findOneBySomeField($value): ?CommentaireRemise
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
