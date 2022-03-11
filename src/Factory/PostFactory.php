<?php

declare(strict_types=1);

namespace App\Factory;

use App\Domain\App\Post;
use App\Entity\Post as PostEntity;

class PostFactory
{
    /**
     * @param PostEntity $postEntity
     * @return Post
     */
    public static function createFromEntity(PostEntity $postEntity): Post
    {
        $post = new Post();
        $post->setText($postEntity->getText());
        $post->setPerex($postEntity->getPerex());
        $post->setTitle($postEntity->getTitle());
        return $post;
    }
}
