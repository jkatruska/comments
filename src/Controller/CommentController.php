<?php

declare(strict_types=1);

namespace App\Controller;

use App\Annotation\RateLimiter;
use App\Exception\ValidationException;
use App\Response\Error;
use App\Service\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    private CommentService $commentService;

    /**
     * @param CommentService $commentService
     */
    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * @Route("/comment/{id}/reply", name="comment_reply")
     * @RateLimiter(name="comments", identifier="ip")
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
}
