<?php

namespace App\Repository;

use App\Entity\Ligneversementfrais;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ligneversementfrais>
 *
 * @method Ligneversementfrais|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ligneversementfrais|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ligneversementfrais[]    findAll()
 * @method Ligneversementfrais[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LigneversementfraisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ligneversementfrais::class);
    }
    public function searchResult($client = null, $datedebut = null, $datefin = null)
    {
        $sql = $this->createQueryBuilder('li')

            ->join('li.compte', 'compte') // Relation Ligneversementfrais -> Compte
            ->join('compte.client', 'client') 
            ->join('client.identifications', 'id')
            ->join('id.dossier', 'd')
            ->addSelect('compte', 'client', 'id', 'd');



        if ($client  || $datedebut || $datefin) {

            if ($client != "null") {
                $sql->andWhere('client.id = :client')
                ->setParameter('client', $client);
            }


            // dd($datedebut);

            if ($datedebut != null && $datefin == null) {
                $truc = explode('-', str_replace("/", "-", $datedebut));
                $new_date_debut = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                $sql->andWhere('li.dateversementfrais = :datedebut')
                ->setParameter('datedebut', $new_date_debut);
            }
            if ($datefin != "null" && $datedebut == "null") {

                $truc = explode('-', str_replace("/", "-", $datefin));
                $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                $sql->andWhere('li.dateversementfrais  = :datefin')
                ->setParameter('datefin', $new_date_fin);
            }
            if ($datedebut != "null" && $datefin != "null") {

                $truc_debut = explode('-', str_replace("/", "-", $datedebut));
                $new_date_debut = $truc_debut[2] . '-' . $truc_debut[1] . '-' . $truc_debut[0];

                $truc = explode('-', str_replace("/", "-", $datefin));
                $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                $sql->andWhere('li.dateversementfrais BETWEEN :datedebut AND :datefin')
                ->setParameter('datedebut', $new_date_debut)
                    ->setParameter("datefin", $new_date_fin);
            }
        }

        // Retourner les résultats
        return $sql->getQuery()->getResult();
    }
//    /**
//     * @return Ligneversementfrais[] Returns an array of Ligneversementfrais objects
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

//    public function findOneBySomeField($value): ?Ligneversementfrais
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
