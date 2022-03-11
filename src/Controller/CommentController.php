<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\RateLimitException;
use App\Exception\ValidationException;
use App\Response\Error;
use App\Service\CommentService;
use App\Util\RateLimiter;
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
     * @param int
     * @param Request $request
     * @param RateLimiter $limiter
     * @return JsonResponse
     */
    public function replyToComment(int $id, Request $request, RateLimiter $limiter): JsonResponse
    {
        $limiterKey = 'comments.' . $_SERVER['REMOTE_ADDR'];

        try {
            $limiter->check($limiterKey, RateLimiter::MAX_COMMENTS);
            $data = $request->request->all();
            if ($data) {
                $this->commentService->replyToComment($id, $data);
            }
            $limiter->increment($limiterKey);
            return $this->json(null, Response::HTTP_NO_CONTENT);
        } catch (RateLimitException $limitException) {
            return $this->json(Error::new($limitException->getMessage()), Response::HTTP_TOO_MANY_REQUESTS);
        } catch (ValidationException $invalidRequestException) {
            return $this->json(Error::new($invalidRequestException->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }
}
