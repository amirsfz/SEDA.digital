<?php

declare(strict_types=1);

namespace SEDA\Tracing\Tracer;

use DDTrace\Contracts\Tracer;
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
            $span->overwriteOperationName($name);
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
        // @TODO this is so hacky, there might be a better way to do so!
//        $tracerWrapperReflection = new \ReflectionClass($this->tracer);
//        $property = $tracerWrapperReflection->getProperty('tracer');
//        $property->setAccessible(true);
//        $tracer = $property->getValue($this->tracer);

        $tracerReflection = new \ReflectionClass($this->tracer);
        $configProperty = $tracerReflection->getProperty('config');
        $configProperty->setAccessible(true);
        $configValue = $configProperty->getValue($this->tracer);

        $configValue['enabled'] = false;
        $configProperty->setValue($this->tracer, $configValue);
    }

    private function getConfig(): array
    {
        return $this->config;
    }
}
