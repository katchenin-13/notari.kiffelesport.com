<?php

namespace App\Repository;

use App\Entity\EnregistrementDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnregistrementDocument>
 *
 * @method EnregistrementDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnregistrementDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnregistrementDocument[]    findAll()
 * @method EnregistrementDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnregistrementDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnregistrementDocument::class);
    }

//    /**
//     * @return EnregistrementDocument[] Returns an array of EnregistrementDocument objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EnregistrementDocument
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
