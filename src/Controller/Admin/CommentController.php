<?php

namespace App\Controller\Admin;

use App\Exception\ValidationException;
use App\Response\Error;
use App\Service\CommentService;
use App\Service\PostService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    private CommentService $commentService;
    private PostService $postService;

    public function __construct(CommentService $commentService, PostService $postService)
    {
        $this->commentService = $commentService;
        $this->postService = $postService;
    }

    /**
     * @Route("/comments", name="admin_comments", methods={"GET"})
     */
    public function getAll(): Response
    {
        $comments = $this->commentService->getAll();
        $posts = $this->postService->getAll();
        return $this->render(
            'admin/comments.html.twig',
            [
                'comments' => $comments,
                'posts' => $posts,
                'addCommentUrl' => $this->generateUrl('admin_add_comment'),
                'deleteCommentUrl' => $this->generateUrl('admin_delete_comment', ['id' => '-id-']),
                'updateCommentUrl' => $this->generateUrl('admin_update_comment', ['id' => '-id-']),
                'replyToCommentUrl' => $this->generateUrl('admin_reply_comment', ['id' => '-id-'])
            ]
        );
    }

    /**
     * @Route("/comment", name="admin_add_comment", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        try {
            $data = $request->request->all();
            $slug = $data['slug'] ?? null;
            unset($data['slug']);
            if (!$data || !$slug) {
                throw new ValidationException("Missing slug");
            }
            $this->commentService->addComment($slug, $data);
            return $this->json(null, Response::HTTP_NO_CONTENT);
        } catch (ValidationException $invalidRequestException) {
            return $this->json(Error::new($invalidRequestException->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/comment/{id}/reply", name="admin_reply_comment")
     * @param int
     * @param Request $request
     * @return JsonResponse
     */
    public function replyToComment(int $id, Request $request): JsonResponse
    {
        try {
            $data = $request->request->all();
            if ($data) {
                $this->commentService->replyToComment($id, $data);
            }
            return $this->json(null, Response::HTTP_NO_CONTENT);
        } catch (ValidationException $invalidRequestException) {
            return $this->json(Error::new($invalidRequestException->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/comment/{id}", name="admin_delete_comment", methods={"DELETE"})
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $this->commentService->delete($id);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/comment/{id}", name="admin_update_comment", methods={"PUT"})
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $this->commentService->update($id, $request->request->all());
        } catch (ValidationException $invalidRequestException) {
            return $this->json(Error::new($invalidRequestException->getMessage()), Response::HTTP_BAD_REQUEST);
        }
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}