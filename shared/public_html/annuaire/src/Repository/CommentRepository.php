<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function getCommentsByUser($code): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.id', 'c.comment', 'c.sender', 'c.stars', 'u.username', 'u.avatar', 'u.name', 'u.lastname', 'u.id AS user_id')
            ->join('App\Entity\User', 'u', 'WITH', 'c.sender = u.code')
            ->where('c.recipient = :recipient')
            ->setParameter('recipient', $code)
            ->getQuery()
            ->getArrayResult();
    }

    public function getAvgStarsByUser($code): int
    {
        $avg = $this->createQueryBuilder('c')
            ->select('AVG(c.stars) AS avg_stars')
            ->where('c.recipient = :recipient')
            ->setParameter('recipient', $code)
            ->getQuery()
            ->getArrayResult();

        return round($avg[0]['avg_stars']);
    }

    public function canComment($sender, $recipient): bool
    {
        if ($sender === $recipient) {
            return false;
        }
        $comment = $this->createQueryBuilder('c')
            ->select('c.id')
            ->where('c.sender = :sender')
            ->andWhere('c.recipient = :recipient')
            ->setParameter('sender', $sender)
            ->setParameter('recipient', $recipient)
            ->getQuery()
            ->getArrayResult();

        return count($comment) === 0;
    }
}
