<?php

namespace App\Factory;

use App\Domain\App\Comment;

class CommentFactory
{
    /**
     * @param array $comment
     * @return Comment
     */
    public static function createFromArray(array $comment): Comment
    {
        $commentDTO = new Comment();
        $commentDTO->setId($comment['id']);
        $commentDTO->setAuthor($comment['author']);
        $commentDTO->setText($comment['comment']);
        $commentDTO->setParentId($comment['parentId']);
        $commentDTO->setDateAdd($comment['dateAdd']);
        $commentDTO->setPostId($comment['postId']);
        return $commentDTO;
    }
}