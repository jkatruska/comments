<?php

namespace App\Service;

use App\Domain\App\Comment;
use App\Entity\Comment as CommentEntity;
use App\Entity\Post as PostEntity;
use App\Exception\ValidationException;
use App\Factory\CommentFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommentService
{
    private EntityManagerInterface $em;

    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * @param string $postSlug
     * @param array $data
     * @throws ValidationException
     */
    public function addComment(string $postSlug, array $data)
    {
        $this->validateComment($data);
        $postRepo = $this->em->getRepository(PostEntity::class);
        $post = $postRepo->findOneBy(['slug' => $postSlug]);

        $comment = new CommentEntity();
        $comment->setAuthor($data['author']);
        $comment->setComment($data['text']);
        $comment->setPostId($post->getId());
        $this->em->persist($comment);
        $this->em->flush();
    }

    /**
     * @param int $id
     * @param array $data
     * @throws ValidationException
     */
    public function replyToComment(int $id, array $data)
    {
        $this->validateComment($data);
        $commentsRepo = $this->em->getRepository(CommentEntity::class);
        /** @var CommentEntity $parentComment */
        $parentComment = $commentsRepo->find($id);

        $comment = new CommentEntity();
        $comment->setAuthor($data['author']);
        $comment->setComment($data['text']);
        $comment->setPostId($parentComment->getPostId());
        $comment->setParentId($id);

        $this->em->persist($comment);
        $this->em->flush();
    }

    /**
     * @param int $id
     * @param array $data
     * @throws ValidationException
     */
    public function update(int $id, array $data)
    {
        $this->validateComment($data);
        $commentsRepo = $this->em->getRepository(CommentEntity::class);
        /** @var CommentEntity $comment */
        $comment = $commentsRepo->find($id);
        $comment->setComment($data['text']);
        $comment->setAuthor($data['author']);
        $this->em->flush();
    }

    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        $commentsRepo = $this->em->getRepository(CommentEntity::class);
        $commentsRepo->delete($id);
    }

    /**
     * @param PostEntity $post
     * @return Comment[]
     */
    public function getByPost(PostEntity $post): array
    {
        $commentsRepo = $this->em->getRepository(CommentEntity::class);
        $commentsArr = $commentsRepo->getByPost($post);
        $comments = array_map(fn($comment) => CommentFactory::createFromArray($comment), $commentsArr);
        foreach ($comments as $comment) {
            $this->assignReplies($comment, $comments);
        }
        return $comments;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        $commentsRepo = $this->em->getRepository(CommentEntity::class);
        $commentsArr = $commentsRepo->getAll();
        $comments = array_map(fn($comment) => CommentFactory::createFromArray($comment), $commentsArr);
        foreach ($comments as $comment) {
            $this->assignReplies($comment, $comments);
        }
        return $comments;
    }

    /**
     * @throws ValidationException
     */
    private function validateComment(array $data)
    {
        $constraints = new Collection(
            [
                'author' => [new NotBlank(), new Length(['max' => 128])],
                'text' => new NotBlank()
            ]
        );
        $errors = $this->validator->validate($data, $constraints);
        if (!empty($errors->count())) {
            $formattedErrors = [];
            /** @var ConstraintViolationInterface $error */
            foreach ($errors as $error) {
                $formattedErrors[] = $error->getMessage() . ' ' . $error->getPropertyPath();
            }
            throw new ValidationException(implode(', ', $formattedErrors));
        }
    }

    /**
     * @param Comment $comment
     * @param Comment[] $comments
     */
    private function assignReplies(Comment $comment, array &$comments): void
    {
        $replies = array_filter($comments, fn($entity) => $entity->getParentId() === $comment->getId());
        if (!$replies) {
            return;
        }
        foreach ($replies as $key => $reply) {
            unset($comments[$key]);
            $this->assignReplies($reply, $comments);
            $comment->addReply($reply);
        }
    }
}