<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    // Cette requête retourne l'ensemble des messages reçus et non lus par l'utilisateur
    public function findAllMessagesByUser(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.destinataire = :user')
            ->andWhere('m.lu = false')
            ->andWhere('m.supprime = false')
            ->setParameter('user', $user)
            ->orderBy('m.dateEnvoi', 'DESC');

        return $qb->getQuery()->getResult();
    }

    // Récupérer un nombre de messages à partir d'un offset
    public function getMessageFromOffset(Utilisateur $user, $limit, $offset)
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.destinataire = :user')
            ->andWhere('m.supprime = false')
            ->setParameter('user', $user)
            ->orderBy('m.dateEnvoi', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $qb->getQuery()->getResult();
    }

    // Récupérer le nombre de messages reçus par un utilisateur
    public function getNbMessagesRecus(Utilisateur $user): int
    {
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.destinataire = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // Récupérer les messages favoris à partir d'un offset
    public function getMessageFavorisFromOffset(Utilisateur $user, $limit, $offset)
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.destinataire = :user')
            ->andWhere('m.supprime = false')
            ->andWhere('m.favoris = true')
            ->setParameter('user', $user)
            ->orderBy('m.dateEnvoi', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $qb->getQuery()->getResult();
    }

    // Récupérer le nombre de messages favoris
    public function getNbMessagesFavoris(Utilisateur $user): int
    {
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m)')
            ->where('m.destinataire = :user')
            ->andWhere('m.supprime = false')
            ->andWhere('m.favoris = true')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // Récupérer les messages dans la corbeille à partir d'un offset
    public function getMessageCorbeilleFromOffset(Utilisateur $user, $limit, $offset)
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.destinataire = :user')
            ->andWhere('m.supprime = true')
            ->setParameter('user', $user)
            ->orderBy('m.dateEnvoi', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $qb->getQuery()->getResult();
    }

    // Récupérer le nombre de messages dans la corbeille
    public function getNbMessagesCorbeille(Utilisateur $user): int
    {
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m)')
            ->where('m.destinataire = :user')
            ->andWhere('m.supprime = true')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // Recherche des messages
    public function search(Utilisateur $user, $limit, $offset, $motcle, $supprime = null, $favoris = null)
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.expediteur', 'u')
            ->where('m.destinataire = :user')
            ->andWhere('m.objetMessage LIKE :motcle OR u.nom LIKE :motcle OR u.prenom LIKE :motcle')
            ->setParameter('user', $user)
            ->setParameter('motcle', '%'.$motcle.'%')
            ->orderBy('m.dateEnvoi', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($favoris !== null) {
            $qb->andWhere('m.favoris = :favoris')
                ->setParameter('favoris', $favoris);
        }

        if ($supprime !== null) {
            $qb->andWhere('m.supprime = :supprime')
                ->setParameter('supprime', $supprime);
        }

        return $qb->getQuery()->getResult();
    }

    // Nombre de résultats de recherche
    public function getNbsearch(Utilisateur $user, $motcle, $supprime = null, $favoris = null): int
    {
        $qb = $this->createQueryBuilder('m')
            ->select('COUNT(m)')
            ->leftJoin('m.expediteur', 'u')
            ->where('m.destinataire = :user')
            ->andWhere('m.objetMessage LIKE :motcle OR u.nom LIKE :motcle OR u.prenom LIKE :motcle')
            ->setParameter('user', $user)
            ->setParameter('motcle', '%'.$motcle.'%');

        if ($favoris !== null) {
            $qb->andWhere('m.favoris = :favoris')
                ->setParameter('favoris', $favoris);
        }

        if ($supprime !== null) {
            $qb->andWhere('m.supprime = :supprime')
                ->setParameter('supprime', $supprime);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
    /**
     * @param Utilisateur $user
     * @return int
     */
    public function countUnreadMessages(Utilisateur $user): int
    {
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.destinataire = :user')
            ->andWhere('m.lu = false') // Adjust field names based on your entity
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param Utilisateur $user
     * @return Message[]
     */
    public function findUnreadMessages(Utilisateur $user): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.destinataire = :user')
            ->andWhere('m.lu = false') // Adjust field names based on your entity
            ->setParameter('user', $user)
            ->orderBy('m.dateEnvoi', 'DESC') // Adjust field names based on your entity
            ->getQuery()
            ->getResult();
    }
}

