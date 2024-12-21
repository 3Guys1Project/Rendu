<?php

namespace App\Repository;

use App\Entity\Participations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Participations>
 */
class ParticipationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participations::class);
    }

    public function checkIfParticipationExists($userId, $eventId): bool
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('COUNT(p.id)');
        $qb->andWhere('p.event = :eventId');
        $qb->andWhere('p.user = :userId');
        $qb->setParameter('eventId', $eventId);
        $qb->setParameter('userId', $userId);
        $count = $qb->getQuery()->getSingleScalarResult();
        return $count > 0;
    }

    public function getParticipationById($participationId)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->andWhere('p.id = :participationId');
        $qb->setParameter('participationId', $participationId);
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getParticipationByData($userId, $eventId)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p');
        $qb->andWhere('p.event = :eventId');
        $qb->andWhere('p.user = :userId');
        $qb->setParameter('eventId', $eventId);
        $qb->setParameter('userId', $userId);
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getCountOfParticipationsByEvent($eventId): int
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('COUNT(p.id)');
        $qb->andWhere('p.event = :eventId');
        $qb->setParameter('eventId', $eventId);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getAllFromUserId($userId): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->andWhere('p.user = :userId');
        $qb->setParameter('userId', $userId);
        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Participations[] Returns an array of Participations objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Participations
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function deleteParticipation(mixed $participationId): void
    {
        $qb = $this->createQueryBuilder('p');
        $qb->delete();
        $qb->andWhere('p.id = :participationId');
        $qb->setParameter('participationId', $participationId);
        $qb->getQuery()->execute();
    }
}
