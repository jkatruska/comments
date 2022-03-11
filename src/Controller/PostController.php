<?php

namespace App\Controller;

use App\Exception\RateLimitException;
use App\Exception\ValidationException;
use App\Response\Error;
use App\Service\CommentService;
use App\Service\PostService;
use App\Util\RateLimiter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private PostService $postService;

    private CommentService $commentService;

    /**
     * @param PostService $postService
     * @param CommentService $commentService
     */
    public function __construct(PostService $postService, CommentService $commentService)
    {
        $this->postService = $postService;
        $this->commentService = $commentService;
    }

    /**
     * @Route("/posts", name="posts", methods={"GET"})
     */
    public function list(): Response
    {
        $posts = $this->postService->getAll();
        return $this->render('posts.html.twig', ['posts' => $posts]);
    }

    /**
     * @Route("/post/{slug}", name="post_detail", methods={"GET"})
     */
    public function detail(string $slug): Response
    {
        $post = $this->postService->getDetail($slug);
        return $this->render(
            'post_detail.html.twig',
            [
                'post' => $post,
                'addCommentUrl' => $this->generateUrl('post_new_comment', ['slug' => $slug]),
                'replyToCommentUrl' => $this->generateUrl('comment_reply', ['id' => '-id-'])
            ]
        );
    }

    /**
     * @Route("/post/{slug}/comment", name="post_new_comment", methods={"POST"})
     * @param string $slug
     * @param Request $request
     * @param RateLimiter $limiter
     * @return JsonResponse
     */
    public function addComment(string $slug, Request $request, RateLimiter $limiter): JsonResponse
    {
        $limiterKey = 'comments.' . $_SERVER['REMOTE_ADDR'];
        try {
            $limiter->check($limiterKey, RateLimiter::MAX_COMMENTS);
            $data = $request->request->all();
            if ($data) {
                $this->commentService->addComment($slug, $data);
            }
            return $this->json(null, Response::HTTP_NO_CONTENT);
        } catch (RateLimitException $limitException) {
            return $this->json(Error::new($limitException->getMessage()), Response::HTTP_TOO_MANY_REQUESTS);
        } catch (ValidationException $invalidRequestException) {
            return $this->json(Error::new($invalidRequestException->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }
}