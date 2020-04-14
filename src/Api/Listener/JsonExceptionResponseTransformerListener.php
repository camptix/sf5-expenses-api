<?php

declare(strict_types=1);

namespace App\Api\Listener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class JsonExceptionResponseTransformerListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $data = [
                'class' => \get_class($exception),
                'code' => $exception->getStatusCode(),
                'message' => $exception->getMessage(),
            ];

            $response = $this->prepareResponse($data, $data['code']);

            $event->setResponse($response);
        }
    }

    private function prepareResponse(array $data, int $statusCode): Response
    {
        $response = new JsonResponse($data, $statusCode);
        $response->headers->set('Server-Time', \time());
        $response->headers->set('X-Error-Code', $statusCode);

        return $response;
    }
}
