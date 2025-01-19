<?php

namespace App\Repository;

use App\Entity\Dossier;
use App\Entity\Employe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dossier>
 *
 * @method Dossier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dossier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dossier[]    findAll()
 * @method Dossier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DossierRepository extends ServiceEntityRepository
{
    use TableInfoTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dossier::class);
    }

    public function add(Dossier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Dossier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }



    public function getListe($etat, $titre)
    {
        return $this->createQueryBuilder("d")
            ->innerJoin('d.typeActe', 't')
            ->where('d.active=:active')
            ->andWhere('d.etat=:etat')
            ->andWhere('t.titre=:titre')
            ->setParameters(array('active' => 1, 'etat' => $etat, 'titre' => $titre))
            ->getQuery()
            ->getResult();
    }

 

    public function getDossier($id)
    {
        return $this->createQueryBuilder('d')
           
            ->innerJoin('d.employe', 'e')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery();
 // Retourne une seule valeur
    }

    public function findEmployeDossier($clair)
    {
        $qb = $this->createQueryBuilder('d')
            ->select('e.nom, e.prenom')
            ->innerJoin('d.employe', 'e')
            ->setMaxResults(1);
        if ($clair !== null) {
                    $qb->andWhere('e.id = :clair')
                        ->setParameter('clair', $clair);
        }
        return $qb->getQuery()->getOneOrNullResult();
    }


    public function getListeDossierNative(int $clair): array
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        // Requête SQL avec les jointures et les conditions
        $sql = <<<SQL
SELECT 
    d.*, 
    CONCAT(e.nom, ' ', e.prenom) AS employe_nom_prenom, 
    t.titre AS type_acte_nom
FROM 
    dossier d
INNER JOIN 
    _admin_employe e ON d.employe_id = e.id
INNER JOIN 
    type_acte t ON d.type_acte_id = t.id
WHERE 
    d.active = 1
    AND (:clair IS NULL OR e.id = :clair)
ORDER BY 
    d.id DESC
SQL;

        // Définition des paramètres
        $params = [
            'clair' => $clair, // L'ID de l'employé
        ];

        // Exécution de la requête
        $stmt = $connection->executeQuery($sql, $params);

        // Récupération des résultats sous forme associative
        return $stmt->fetchAllAssociative();
    }





    //    /**
    //     * @return Dossier[] Returns an array of Dossier objects
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

    //    public function findOneBySomeField($value): ?Dossier
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
