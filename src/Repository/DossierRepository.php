<?php

namespace App\Repository;

use App\Entity\Dossier;
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
    public function findDossiersByEmploye($cler)
    {
        $query = $this->createQueryBuilder('d')
            ->select('
            d.numeroOuverture, 
            d.numcompte, 
            d.dateCreation, 
            d.objet, 
            e.nom AS employe, 
            d.natureDossier AS nature, 
            t.nom AS typeActe, 
            d.etape
        ')
            ->join('d.employe', 'e')
            ->join('d.typeActe', 't')
            ->where('e.id = :employeId')
            ->setParameter('employeId', $cler)
            ->getQuery();

     

        // This should return an array of results (multiple rows)
        return $query->getResult();
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

    // public function listeActe($acte)
    // {
    //     return $this->createQueryBuilder("d")
    //         ->innerJoin('d.typeActe', 't')
    //         ->where('d.active=:active')
    //         ->andWhere('t.id=:acte')
    //         ->setParameters(array('active' => 1, 'acte' => $acte))
    //         ->getQuery()
    //         ->getResult();
    // }

    public function getEmployeNomPrenom($id)
    {
        $qb = $this->createQueryBuilder('d')
            ->select('CONCAT(e.nom, \' \', e.prenom)')
            ->innerJoin('d.employe', 'e')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $qb->getSingleScalarResult();  // Retourne une seule valeur
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

    public function countAll($etat, $searchValue = null)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $sql = <<<SQL
SELECT COUNT(id)
FROM dossier
WHERE  1 = 1
SQL;
        $params = [];

        if ($etat == 'termine') {
            $sql .= " AND (JSON_CONTAINS(etat, '1', '$.termine') = 1)";
        } elseif ($etat == 'archive') {
            $sql .= " AND (JSON_CONTAINS(etat, '1', '$.archive') = 1)";
        } else {
            $sql .= " AND ((JSON_CONTAINS(etat, '1', '$.cree') = 1) or (JSON_CONTAINS(etat, '1', '$.en_cours')= 1))";
        }



        $sql .= $this->getSearchColumns($searchValue, $params, ['d.numero_ouverture']);



        $stmt = $connection->executeQuery($sql, $params);


        return intval($stmt->fetchOne());
    }



    public function getAll($etat, $limit, $offset, $searchValue = null)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $sql = <<<SQL
SELECT
id,
date_creation,
numero_ouverture,
objet,
etape,
type_acte_id
FROM dossier
WHERE  1 = 1

SQL;
        $params = [];


        if ($etat == 'termine') {
            $sql .= " AND (JSON_CONTAINS(etat, '1', '$.termine') = 1)";
        } elseif ($etat == 'archive') {
            $sql .= " AND (JSON_CONTAINS(etat, '1', '$.archive') = 1)";
        } else {
            $sql .= " AND ((JSON_CONTAINS(etat, '1', '$.cree') = 1) or (JSON_CONTAINS(etat, '1', '$.en_cours') = 1))";
        }

        $sql .= $this->getSearchColumns($searchValue, $params, ['d.numero_ouverture']);

        $sql .= ' ORDER BY id DESC';

        if ($limit && $offset == null) {
            $sql .= " LIMIT {$limit}";
        } else if ($limit && $offset) {
            $sql .= " LIMIT {$offset},{$limit}";
        }



        $stmt = $connection->executeQuery($sql, $params);
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
