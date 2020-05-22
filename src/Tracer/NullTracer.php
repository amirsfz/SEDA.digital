<?php

declare(strict_types=1);

namespace SEDAdigital\Tracing\Tracer;

final class NullTracer implements TracerInterface
{
    public function configure(): TracerInterface
    {
        return $this;
    }

    public function setTransactionName(string $name): TracerInterface
    {
        return $this;
    }

    public function setServiceName(string $name): TracerInterface
    {
        return $this;
    }

    public function setOperationName(string $name): TracerInterface
    {
        return $this;
    }

    public function setTags(array $tags): TracerInterface
    {
        return $this;
    }

    public function ignoreTransaction(): void
    {
        // noop
    }
}
