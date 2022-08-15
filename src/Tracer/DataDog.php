<?php

declare(strict_types=1);

namespace SEDAdigital\Tracing\Tracer;

use DDTrace\Contracts\Tracer;
use DDTrace\NoopTracer;
use DDTrace\Tag;

final class DataDog implements TracerInterface
{
    use FlattenTags;

    /**
     * @var Tracer
     */
    private $tracer;
    /**
     * @var array
     */
    private $config;
    /**
     * @var bool
     */
    private $configured = false;

    public function __construct(Tracer $tracer, array $config = [])
    {
        $this->tracer = $tracer;
        $this->config = $config;
    }

    public function configure(): TracerInterface
    {
        if ($this->configured) {
            return $this;
        }
        foreach ($this->getConfig() as $key => $value) {
            putenv("{$key}={$value}");
        }
        $this->configured = true;

        return $this;
    }

    public function setTransactionName(string $name): TracerInterface
    {
        $span = $this->tracer->getSafeRootSpan();
        if ($span) {
            $span->setTag(Tag::RESOURCE_NAME, $name);
        }

        return $this;
    }

    public function setServiceName(string $name): TracerInterface
    {
        $span = $this->tracer->getSafeRootSpan();
        if ($span) {
            $span->setTag(Tag::SERVICE_NAME, $name);
        }
        return $this;
    }

    public function setOperationName(string $name): TracerInterface
    {
        $span = $this->tracer->getSafeRootSpan();
        if ($span) {
            $span->overwriteOperationName($name);
        }

        return $this;
    }

    /**
     * @see Tag
     *
     * @param array $tags
     *
     * @return $this
     */
    public function setTags(array $tags): TracerInterface
    {
        $span = $this->tracer->getSafeRootSpan();
        if (null === $span) {
            return $this;
        }
        // @TODO map tags to relevant ones
        foreach ($this->getFlattenedTags($tags) as $key => $value) {
            $span->setTag($key, $value);
        }

        return $this;
    }

    public function ignoreTransaction(): void
    {
        $this->tracer = new NoopTracer();
    }

    private function getConfig(): array
    {
        return $this->config;
    }
}
