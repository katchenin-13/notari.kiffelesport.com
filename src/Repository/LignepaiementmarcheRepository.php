<?php

namespace App\Repository;

use App\Entity\Lignepaiementmarche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepaiementmarche>
 *
 * @method Lignepaiementmarche|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepaiementmarche|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepaiementmarche[]    findAll()
 * @method Lignepaiementmarche[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepaiementmarcheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepaiementmarche::class);
    }

    public function add(Lignepaiementmarche $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Lignepaiementmarche $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();    
            }
    } 
    
    public function searchResult($marche, $datedebut, $datefin)
    {
        $sql = $this->createQueryBuilder('l')

        ->join('l.marche', 'marche')
        ->join('l.comptefour', 'comptefour') 
      
        ->addSelect('marche', 'comptefour');

         if ($marche  || $datedebut || $datefin) {
            if ($marche != "null") {
                $sql->andWhere('marche.id = :marche')
                ->setParameter('marche', $marche);
            }

            if ($datedebut != null && $datefin == null) {
                $truc = explode('-', str_replace("/", "-", $datedebut));
                $new_date_debut = $truc[2] . '-' . $truc[1] . '-' . $truc[0];
                $sql->andWhere('l.datepaiement = :datedebut')
                ->setParameter('datedebut', $new_date_debut);
            }
            if ($datefin != "null" && $datedebut == "null") {
                $truc = explode('-', str_replace("/", "-", $datefin));
                $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];
                $sql->andWhere('l.datepaiement = :datefin')
                ->setParameter('datefin', $new_date_fin);
            }

            if ($datedebut != null && $datefin != null) {
                $truc = explode('-', str_replace("/", "-", $datedebut));
                $new_date_debut = $truc[2] . '-' . $truc[1] . '-' . $truc[0];
                $truc = explode('-', str_replace("/", "-", $datefin));
                $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];
                $sql->andWhere('l.datepaiement BETWEEN :datedebut AND :datefin')
                ->setParameter('datedebut', $new_date_debut)
                ->setParameter('datefin', $new_date_fin);
            }
            # code...
         }
        return $sql->getQuery()->getResult();
    }

//    /**
//     * @return Lignepaiementmarche[] Returns an array of Lignepaiementmarche objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Lignepaiementmarche
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
