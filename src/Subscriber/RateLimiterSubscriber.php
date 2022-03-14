<?php

namespace App\Subscriber;

use App\Exception\RateLimitException;
use App\Response\Error;
use App\Util\RateLimiter;
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

class RateLimiterSubscriber implements EventSubscriberInterface
{
    private RateLimiter $rateLimiter;
    private Reader $reader;
    private SerializerInterface $serializer;

    public function __construct(RateLimiter $rateLimiter, Reader $reader, SerializerInterface $serializer)
    {
        $this->rateLimiter = $rateLimiter;
        $this->reader = $reader;
        $this->serializer = $serializer;
    }

    /**
     * @return string[][]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController'],
            KernelEvents::RESPONSE => ['onKernelResponse'],
        ];
    }

    /**
     * @param ControllerEvent $event
     * @throws ReflectionException
     */
    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();
        $method = null;
        if (is_array($controller)) {
            $method = $controller[1];
            $controller = $controller[0];
        }

        if (!$method) {
            return;
        }

        $ref = new ReflectionClass($controller);
        $method = $ref->getMethod($method);
        $isLimited = false;
        $methodAnnotations = $this->reader->getMethodAnnotations($method);
        $limiter = null;

        foreach ($methodAnnotations as $methodAnnotation) {
            if ($methodAnnotation instanceof \App\Annotation\RateLimiter) {
                $isLimited = true;
                $limiter = $methodAnnotation;
                break;
            }
        }

        if (!$isLimited) {
            return;
        }

        $key = $limiter->getName() . '.' . $this->getUserIdentifier($limiter->getIdentifier());
        $event->getRequest()->attributes->set('limiterKey', $key);
        $event->getRequest()->attributes->set('limiterTimeout', $limiter->getTimeout());
        try {
            $this->rateLimiter->check($key, $limiter->getLimit());
        } catch (RateLimitException $e) {
            $error = Error::new($e->getMessage());
            $error = $this->serializer->serialize($error, 'json');
            $event->setController(function () use ($error) {
                return new JsonResponse($error, Response::HTTP_TOO_MANY_REQUESTS, [], true);
            });
        }
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$limiterKey = $request->attributes->get('limiterKey')) {
            return;
        }

        if ($this->isSucessfullResponse($event->getResponse())) {
            $this->rateLimiter->increment($limiterKey, $request->attributes->get('limiterTimeout'));
        }
    }

    /**
     * @param string $identifier
     * @return string
     */
    private function getUserIdentifier(string $identifier): string
    {
        return match ($identifier) {
            'ip' => $_SERVER['REMOTE_ADDR']
        };
    }

    /**
     * @param Response $response
     * @return bool
     */
    private function isSucessfullResponse(Response $response): bool
    {
        return in_array($response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_CREATED,
            Response::HTTP_ACCEPTED,
            Response::HTTP_NON_AUTHORITATIVE_INFORMATION,
            Response::HTTP_NO_CONTENT,
            Response::HTTP_RESET_CONTENT,
            Response::HTTP_PARTIAL_CONTENT,
            Response::HTTP_MULTI_STATUS,
            Response::HTTP_ALREADY_REPORTED,
            Response::HTTP_IM_USED,
        ]);
    }
}
