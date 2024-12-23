<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getUserByUsernameOrEmail($username, $email)
    {
        return $this->createQueryBuilder('u')
            ->select('u.code')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $email)
            ->getQuery()
            ->getArrayResult();
    }

    public function getCardUsernames($isAdmin): array
    {
        $sql = $this->createQueryBuilder('u')
            ->select('u.username,u.code, SUBSTRING(u.bio, 1, 201) AS bio, u.avatar, u.name, u.lastname');
        if ($isAdmin) {
            return $sql->getQuery()->getArrayResult();
        }
        return $sql->where('u.visible = TRUE')
            ->getQuery()
            ->getArrayResult();
    }

    public function getAllInformation($code): array
    {
        return $this->createQueryBuilder('u')
            ->select('u.username,u.code, u.email, u.avatar, u.banner, u.visible, u.name, u.lastname, u.bio, u.phone, u.address, u.country, u.zipCode, u.city, u.website, u.job, u.createdAt, u.updatedAt, u.lastLogin')
            ->where('u.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getArrayResult();
    }

    public function getUserByUsername($username): array
    {
        return $this->createQueryBuilder('u')
            ->select('u.code')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getArrayResult();
    }

    public function getUserByCode($code): array
    {
        return $this->createQueryBuilder('u')
            ->select('u.code')
            ->where('u.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getArrayResult();
    }


    public function findBySearchQuery($query, $isAdmin): array
    {
        $sql = $this->createQueryBuilder('u')
            ->select('u.username, u.code, SUBSTRING(u.bio, 1, 201) AS bio, u.avatar, u.name, u.lastname')
            ->where('LOWER(u.username) LIKE LOWER(:query)')
            ->setParameter('query', '%' . strtolower($query) . '%')
            ->orderBy('u.id', 'ASC');

        if ($isAdmin) {
            return $sql->getQuery()->getArrayResult();
        }
        return $sql->andWhere('u.visible = TRUE')->getQuery()->getArrayResult();
    }
}
