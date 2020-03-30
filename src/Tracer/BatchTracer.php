<?php

declare(strict_types=1);

namespace SEDA\Tracing\Tracer;

final class BatchTracer implements TracerInterface
{
    /**
     * @var TracerInterface[]
     */
    private $tracers;

    /**
     * @param TracerInterface[] $tracers
     */
    public function __construct(array $tracers = [])
    {
        $this->tracers = $tracers;
    }

    public function configure(): TracerInterface
    {
        array_map(static function (TracerInterface $tracer) {
            $tracer->configure();
        }, $this->tracers);

        return $this;
    }

    public function setTransactionName(string $name): TracerInterface
    {
        array_map(static function (TracerInterface $tracer) use ($name) {
            $tracer->setTransactionName($name);
        }, $this->tracers);

        return $this;
    }

    public function setServiceName(string $name): TracerInterface
    {
        array_map(static function (TracerInterface $tracer) use ($name) {
            $tracer->setServiceName($name);
        }, $this->tracers);

        return $this;
    }

    public function setTags(array $tags): TracerInterface
    {
        array_map(static function (TracerInterface $tracer) use ($tags) {
            $tracer->setTags($tags);
        }, $this->tracers);

        return $this;
    }

    public function ignoreTransaction(): void
    {
        array_map(static function (TracerInterface $tracer) {
            $tracer->ignoreTransaction();
        }, $this->tracers);
    }
}
