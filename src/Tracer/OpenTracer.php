<?php

declare(strict_types=1);

namespace SEDAdigital\Tracing\Tracer;

use OpenTracing\Tracer;

abstract class OpenTracer implements TracerInterface
{
    /**
     * @var Tracer
     */
    private $tracer;

    public function __construct(Tracer $tracer)
    {
        $this->tracer = $tracer;
    }

    public function configure(): TracerInterface
    {
        // TODO: Implement configure() method.
        return $this;
    }

    public function setTransactionName(string $name): TracerInterface
    {
        // TODO: Implement setTransactionName() method.
        $span = $this->tracer->getActiveSpan();
        if ($span) {
            //$span->setTag();
        }
        return $this;
    }

    public function setServiceName(string $name): TracerInterface
    {
        // TODO: Implement setServiceName() method.
        return $this;
    }

    public function setOperationName(string $name): TracerInterface
    {
        // TODO: Implement setOperationName() method.
        $span = $this->tracer->getActiveSpan();
        if ($span) {
            $span->overwriteOperationName($name);
        }
        return $this;
    }

    public function setTags(array $tags): TracerInterface
    {
        // TODO: Implement setTags() method.
        return $this;
    }

    public function ignoreTransaction(): void
    {
        // TODO: Implement ignoreTransaction() method.
    }
}
