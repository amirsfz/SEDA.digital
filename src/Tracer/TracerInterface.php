<?php

declare(strict_types=1);

namespace SEDAdigital\Tracing\Tracer;

interface TracerInterface
{
    public function configure(): self;
    public function setTransactionName(string $name): self;
    public function setServiceName(string $name): self;
    public function setOperationName(string $name): self;
    public function setTags(array $tags): self;
    public function ignoreTransaction(): void;
}
