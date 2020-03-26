<?php

declare(strict_types=1);

namespace SEDA\Tracing\Tracer;

use DDTrace\Contracts\Span;
use DDTrace\Contracts\Tracer;
use DDTrace\Tag;

final class DataDog implements TracerInterface
{
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
        //$flat = $this->getFlattenTags($tags);
        // @TODO map tags to relevant ones
        $this->setFlattenTags($tags, $span);
//        foreach ($tags as $key => $value) {
//            if (is_array($value)) {
//                $this->
//            }
//            $span->setTag($key, $value);
//        }

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

//    private function getFlattenTags(array $tags, ?array &$flatten = null, ?string $prefix = null): array
//    {
//        if (null === $flatten) {
//            $flatten = [];
//        }
//        foreach ($tags as $key => $value) {
//            if (is_array($value)) {
//                $prefixKey = $prefix !== null ? "{$prefix}.{$key}" : $key;
//                $this->getFlattenTags($value, $flatten, $prefixKey);
//                continue;
//            }
//            $flatten[$key] = $value;
//        }
//
//        return $flatten;
//    }

    private function setFlattenTags(array $tags, Span $span, ?string $prefix = null): void
    {
        foreach ($tags as $key => $value) {
            if (is_array($value)) {
//                $this->setFlattenTags($value, $span,"{$prefix}.{$key}");
//                continue;
                $value = json_encode($value, JSON_THROW_ON_ERROR, 512);
            }
            $span->setTag($key, $value);
        }
    }

    private function getConfig(): array
    {
        return $this->config;
    }
}
