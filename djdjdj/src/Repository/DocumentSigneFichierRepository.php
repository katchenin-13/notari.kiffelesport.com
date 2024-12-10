<?php

namespace App\Repository;

use App\Entity\DocumentSigneFichier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentSigneFichier>
 *
 * @method DocumentSigneFichier|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentSigneFichier|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentSigneFichier[]    findAll()
 * @method DocumentSigneFichier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentSigneFichierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentSigneFichier::class);
    }

//    /**
//     * @return DocumentSigneFichier[] Returns an array of DocumentSigneFichier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DocumentSigneFichier
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
