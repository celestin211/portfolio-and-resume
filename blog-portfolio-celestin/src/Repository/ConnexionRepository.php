<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Connexion;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\ParameterType;


class ConnexionRepository extends ServiceEntityRepository
{

    public function __construct(EntityManagerInterface $em, ManagerRegistry $registry,)
    {

        parent::__construct($registry, Connexion::class); // Appel au parent pour l'initialisation
    }

    public function findDernieresConnexions(Utilisateur $utilisateur, int $maxResults = 10)
    {
        $qb = $this->createQueryBuilder('connexion');

        $qb->where('connexion.utilisateur = :UTILISATEUR')
            ->orderBy('connexion.dateCreation', 'DESC')
            ->setParameter('UTILISATEUR', $utilisateur)
            ->setMaxResults($maxResults)
        ;

        return $qb->getQuery()->getResult();
    }



    public function getLastConnexions(int $max = 100)
    {
        $subQuery = "
        SELECT
            DATE(connexion.date_connexion) AS date_connexion, 
            COUNT(connexion.date_connexion) AS nb_connexions
        FROM connexion
        GROUP BY DATE(connexion.date_connexion)
        ORDER BY DATE(connexion.date_connexion) DESC
    ";

        $sqlDateConnexion = "
        SELECT DATE_FORMAT(date_connexion, '%d/%m/%Y') AS date_connexion
        FROM (
            $subQuery
        ) AS sub_query
        LIMIT ?
    ";

        $sqlNbConnexions = "
        SELECT nb_connexions
        FROM (
            $subQuery
        ) AS sub_query
        LIMIT ?
    ";

        try {
            // Utilisez getEntityManager() pour récupérer l'Entity Manager
            $connection = $this->getEntityManager()->getConnection();

            // Préparez les requêtes
            $statementDatesConnexions = $connection->prepare($sqlDateConnexion);
            $statementNbConnexions = $connection->prepare($sqlNbConnexions);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Error while preparing the SQL: %s', $e->getMessage()));
        }

        // Lie la valeur de la limite (max)
        $statementDatesConnexions->bindValue(1, $max, \PDO::PARAM_INT);
        $statementNbConnexions->bindValue(1, $max, \PDO::PARAM_INT);

        // Exécutez les requêtes
        $dateConnexion = $statementDatesConnexions->executeQuery()->fetchFirstColumn();
        $nbConnexions = $statementNbConnexions->executeQuery()->fetchFirstColumn();

        // Retournez les résultats inversés pour avoir les plus récents en premier
        return [
            'datesConnexions' => array_reverse($dateConnexion),
            'nbConnexions' => array_reverse($nbConnexions),
        ];
    }



    public function getLastConnexionsPerMonth(int $max = 12)
    {
        // Utilise DATE_FORMAT pour obtenir les connexions par mois
        $subQuery = "
    SELECT
        DATE_FORMAT(connexion.date_connexion, '%Y-%m') AS date_connexion, 
        COUNT(DISTINCT connexion.utilisateur_id) AS nb_users
    FROM connexion
    GROUP BY DATE_FORMAT(connexion.date_connexion, '%Y-%m')
    ORDER BY DATE_FORMAT(connexion.date_connexion, '%Y-%m') DESC
    ";

        $sqlDateConnexion = "
    SELECT date_connexion
    FROM (
        $subQuery
    ) AS subquery
    LIMIT ?
    ";

        $sqlNbUsers = "
    SELECT nb_users
    FROM (
        $subQuery
    ) AS subquery
    LIMIT ?
    ";

        try {
            // Utilisez getEntityManager() pour obtenir la connexion à la base de données
            $connection = $this->getEntityManager()->getConnection();

            // Prépare les requêtes SQL
            $statementDatesConnexions = $connection->prepare($sqlDateConnexion);
            $statementNbUsers = $connection->prepare($sqlNbUsers);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Error while preparing the SQL: %s', $e->getMessage()));
        }

        // Lie la valeur de la limite à la requête
        $statementDatesConnexions->bindValue(1, $max, \PDO::PARAM_INT);
        $statementNbUsers->bindValue(1, $max, \PDO::PARAM_INT);

        // Exécute les requêtes
        $dateConnexion = $statementDatesConnexions->executeQuery()->fetchFirstColumn();
        $nbUsers = $statementNbUsers->executeQuery()->fetchFirstColumn();

        // Retourne les résultats inversés pour l'ordre chronologique
        return [
            'datesConnexions' => array_reverse($dateConnexion),
            'nbUsers' => array_reverse($nbUsers),
        ];
    }

    public function getLastConnexionsPerYear(int $max = 12)
    {
        // Utilise DATE_FORMAT pour obtenir les connexions par année
        $subQuery = "
    SELECT
        DATE_FORMAT(connexion.date_connexion, '%Y') AS date_connexion, 
        COUNT(DISTINCT connexion.utilisateur_id) AS nb_users
    FROM connexion
    GROUP BY DATE_FORMAT(connexion.date_connexion, '%Y') 
    ORDER BY DATE_FORMAT(connexion.date_connexion, '%Y') DESC
    ";

        $sqlDateConnexion = "
    SELECT date_connexion
    FROM (
        $subQuery
    ) AS subquery
    LIMIT ?
    ";

        $sqlNbUsers = "
    SELECT nb_users
    FROM (
        $subQuery
    ) AS subquery
    LIMIT ?
    ";

        try {
            // Utilisez getEntityManager() pour obtenir la connexion à la base de données
            $connection = $this->getEntityManager()->getConnection();

            // Prépare les requêtes SQL
            $statementDatesConnexions = $connection->prepare($sqlDateConnexion);
            $statementNbUsers = $connection->prepare($sqlNbUsers);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Error while preparing the SQL: %s', $e->getMessage()));
        }

        // Lie la valeur de la limite à la requête
        $statementDatesConnexions->bindValue(1, $max, \PDO::PARAM_INT);
        $statementNbUsers->bindValue(1, $max, \PDO::PARAM_INT);

        // Exécute les requêtes
        $dateConnexion = $statementDatesConnexions->executeQuery()->fetchFirstColumn();
        $nbUsers = $statementNbUsers->executeQuery()->fetchFirstColumn();

        // Retourne les résultats inversés pour l'ordre chronologique
        return [
            'datesConnexions' => array_reverse($dateConnexion),
            'nbUsers' => array_reverse($nbUsers),
        ];
    }

}
