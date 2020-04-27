<?php

declare(strict_types=1);

namespace SEDAdigital\Tracing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class HttpMiddleware implements MiddlewareInterface
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

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->handle($request, $handler);
    }

    public function handle(ServerRequestInterface $request, RequestHandlerInterface $handler): responseInterface
    {
        $response = $handler->handle($request);

        $this->tracing->handleHttpRequest($request);

        return $response;
    }
}
