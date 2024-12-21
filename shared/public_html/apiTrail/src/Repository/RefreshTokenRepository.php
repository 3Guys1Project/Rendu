<?php

namespace App\Repository;

use App\Entity\RefreshToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<RefreshToken>
 */
class RefreshTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    public function findByUserLogin($login)
    {
        return $this->createQueryBuilder('r')
            ->select('r.id')
            ->andWhere('r.username = :login')
            ->setParameter('login', $login)
            ->getQuery()
            ->getResult();
    }

    public function deleteById($id): void
    {
        $qb = $this->createQueryBuilder('r');
        $qb->delete();
        $qb->andWhere('r.id = :id');
        $qb->setParameter('id', $id);
        $qb->getQuery()->execute();
    }
}