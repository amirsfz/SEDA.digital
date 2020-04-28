<?php

declare(strict_types=1);

namespace SEDAdigital\Tracing;

use Psr\Http\Message\ServerRequestInterface;
use SEDAdigital\Tracing\Tracer\TracerInterface;
use Slim\Interfaces\RouteInterface as Route;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

final class Tracing
{
    /**
     * @var TracerInterface
     */
    private $tracer;
    /**
     * @var string
     */
    private $appName;
    /**
     * @var string[]
     */
    private $ignoredRoutesPatterns;
    /**
     * @var string[]
     */
    private $ignoredCLICommands;

    /**
     * @param TracerInterface $tracer
     * @param string $appName
     * @param string[] $ignoredRoutesPatterns
     * @param string[] $ignoredCLICommands
     */
    public function __construct(
        TracerInterface $tracer,
        string $appName,
        array $ignoredRoutesPatterns = [],
        array $ignoredCLICommands = []
    ) {
        $this->tracer = $tracer;
        $this->appName = $appName;
        $this->ignoredRoutesPatterns = $ignoredRoutesPatterns;
        $this->ignoredCLICommands = $ignoredCLICommands;

        $this->tracer->configure();
    }

    public function setServiceName(string $name): void
    {
        $this->tracer->setServiceName($name);
    }

    public function setTransactionName(string $name): void
    {
        $this->tracer->setTransactionName($name);
    }

    public function setTags(array $tags): void
    {
        $this->tracer->setTags($tags);
    }

    public function handleHttpRequest(ServerRequestInterface $request): void
    {
        if ($request->getMethod() === 'HEAD') {
            $this->ignoreTransaction();
            return;
        }
        /** @var Route|null $route */
        $route = $request->getAttribute(
            'route', // slim 3
            $request->getAttribute('__route__') // slim 4
        );
        if (null === $route) {
            return;
        }
        $pattern = $this->cleanupHttpRoutePattern(
            $route->getPattern()
        );
        if (\in_array($pattern, $this->ignoredRoutesPatterns, true)) {
            // @TODO maybe also check for real route pattern
            $this->ignoreTransaction();
            return;
        }
        $this->setServiceName("{$this->appName}.http");
        $this->setTransactionName("[{$request->getMethod()}] {$pattern}");
        $this->setTags([
            'slim.route.arguments' => $route->getArguments(),
        ]);
        $this->setTags([
            'http.query_parameters' => $request->getQueryParams(),
        ]);
    }

    public function handleCLICommand(?Command $command = null, ?InputInterface $input = null): void
    {
        if (null === $command || null === $input) {
            $this->ignoreTransaction();
            return;
        }
        if (in_array($command->getName(), $this->ignoredCLICommands, true)) {
            $this->ignoreTransaction();
            return;
        }
        $this->setServiceName("{$this->appName}.cli");
        $this->setTransactionName("[CLI] {$command->getName()}");
        $this->setTags([
            'input.arguments' => $input->getArguments(),
            'input.options' => $input->getOptions(),
        ]);
    }

    public function ignoreTransaction(): void
    {
        $this->tracer->ignoreTransaction();
    }

    private function cleanupHttpRoutePattern(string $pattern): string
    {
        // remove trailing slash pattern
        $pattern = (string) preg_replace('/(\[\/\])$/', '', $pattern);
        // remove regex from pattern
        $pattern = (string) preg_replace('/{([a-z]+)\:([^\/]*)}/', '{$1}', $pattern);

        return $pattern;
    }
}
