<?php

namespace App\Repository;

use App\Entity\DocumentTypeClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentTypeClient>
 *
 * @method DocumentTypeClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentTypeClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentTypeClient[]    findAll()
 * @method DocumentTypeClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentTypeClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentTypeClient::class);
    }

//    /**
//     * @return DocumentTypeClient[] Returns an array of DocumentTypeClient objects
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

//    public function findOneBySomeField($value): ?DocumentTypeClient
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
