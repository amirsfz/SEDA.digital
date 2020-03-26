<?php

declare(strict_types=1);

namespace SEDA\Tracing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class HttpMiddleware
{
    /**
     * @var Tracing
     */
    private $tracing;

    public function __construct(Tracing $tracing)
    {
        $this->tracing = $tracing;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        /** @var ResponseInterface $response */
        $response = $next($request, $response);

        $this->tracing->handleHttpRequest($request);

        return $response;
    }

    public function handle(ServerRequestInterface $request, RequestHandlerInterface $handler): responseInterface
    {
        $response = $handler->handle($request);

        $this->tracing->handleHttpRequest($request);

        return $response;
    }
}
