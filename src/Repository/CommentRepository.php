<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
    /**
     * @param Post $post
     * @return array|null
     */
    public function getByPost(Post $post): ?array
    {
        return $this->createQueryBuilder('c')
            ->select()
            ->orderBy('c.dateAdd', 'ASC')
            ->where('c.postId = :postId')
            ->setParameter('postId', $post->getId())
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array|null
     */
    public function getAll(): ?array
    {
        return $this->createQueryBuilder('c')
            ->select()
            ->orderBy('c.dateAdd', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        $this->createQueryBuilder('c')
            ->delete()
            ->where('c.id = :id')
            ->orWhere('c.parentId = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }
}
