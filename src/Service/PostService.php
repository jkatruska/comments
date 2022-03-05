<?php

namespace App\Service;

use App\Domain\App\Post;
use App\Entity\Post as PostEntity;
use App\Factory\PostFactory;
use Doctrine\ORM\EntityManagerInterface;

class PostService
{
    private EntityManagerInterface $em;
    private CommentService $commentService;

    public function __construct(EntityManagerInterface $em, CommentService $commentService)
    {
        $this->em = $em;
        $this->commentService = $commentService;
    }

    /**
     * @return PostEntity[]
     */
    public function getAll(): array
    {
        $postRepo = $this->em->getRepository(PostEntity::class);
        return $postRepo->findAll();
    }

    /**
     * @param string $slug
     * @return Post|null
     */
    public function getDetail(string $slug): ?Post
    {
        $postRepo = $this->em->getRepository(PostEntity::class);
        $post = $postRepo->findOneBy(['slug' => $slug]);
        if (!$post) {
            return null;
        }
        $comments = $this->commentService->getByPost($post);
        $post = PostFactory::createFromEntity($post);
        $post->setComments($comments);
        return $post;
    }
}