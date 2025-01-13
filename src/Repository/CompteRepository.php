<?php

namespace App\Repository;

use App\Entity\Compte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Compte>
 *
 * @method Compte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Compte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Compte[]    findAll()
 * @method Compte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Compte::class);
    }

    public function searchResultAll($client )
    {
        $sql = $this->createQueryBuilder('c')

            ->join('c.client', 'client')
            ->join('client.identifications', 'id')
            ->join('id.dossier', 'd')
            ->addSelect('client', 'id',);
    

        if ($client ) {

            if ($client != "null") {
                $sql->andWhere('client.id = :client')
                ->setParameter('client', $client);
            }
           

            // dd($datedebut);

            // if ($datedebut != null && $datefin == null) {
            //     $truc = explode('-', str_replace("/", "-", $datedebut));
            //     $new_date_debut = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

            //     $sql->andWhere('c.datecreation = :datedebut')
            //     ->setParameter('datedebut', $new_date_debut);
            // }
            // if ($datefin != "null" && $datedebut == "null") {

            //     $truc = explode('-', str_replace("/", "-", $datefin));
            //     $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

            //     $sql->andWhere('c.datecreation  = :datefin')
            //     ->setParameter('datefin', $new_date_fin);
            // }
            // if ($datedebut != "null" && $datefin != "null") {

            //     $truc_debut = explode('-', str_replace("/", "-", $datedebut));
            //     $new_date_debut = $truc_debut[2] . '-' . $truc_debut[1] . '-' . $truc_debut[0];

            //     $truc = explode('-', str_replace("/", "-", $datefin));
            //     $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

            //     $sql->andWhere('c.datecreation BETWEEN :datedebut AND :datefin')
            //     ->setParameter('datedebut', $new_date_debut)
            //         ->setParameter("datefin", $new_date_fin);
            // }
 }

        // Retourner les résultats
        return $sql->getQuery()->getResult();
    }


//    /**
//     * @return Compte[] Returns an array of Compte objects
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

//    public function findOneBySomeField($value): ?Compte
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
