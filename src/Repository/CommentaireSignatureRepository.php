<?php

namespace App\Repository;

use App\Entity\CommentaireSignature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommentaireSignature>
 *
 * @method CommentaireSignature|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentaireSignature|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentaireSignature[]    findAll()
 * @method CommentaireSignature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaireSignatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentaireSignature::class);
    }

//    /**
//     * @return CommentaireSignature[] Returns an array of CommentaireSignature objects
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

//    public function findOneBySomeField($value): ?CommentaireSignature
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
