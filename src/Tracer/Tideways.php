<?php

declare(strict_types=1);

namespace SEDAdigital\Tracing\Tracer;

final class Tideways implements TracerInterface
{
    public function configure(): TracerInterface
    {
        // TODO: Implement configure() method.
        $this->start();

        return $this;
    }

    public function setTransactionName(string $name): TracerInterface
    {
        if (!$this->hasProfiler()) {
            return $this;
        }
        \Tideways\Profiler::setTransactionName($name);

        return $this;
    }

    public function setServiceName(string $name): TracerInterface
    {
        if (!$this->hasProfiler()) {
            return $this;
        }
        \Tideways\Profiler::setServiceName($name);

        return $this;
    }

    public function setTags(array $tags): TracerInterface
    {
        if (!$this->hasProfiler()) {
            return $this;
        }

        foreach ($tags as $key => $variable) {
            \Tideways\Profiler::setCustomVariable($key, $variable);
        }

        return $this;
    }

    public function ignoreTransaction(): void
    {
        if (!$this->hasProfiler()) {
            return;
        }

        \Tideways\Profiler::ignoreTransaction();
    }

    private function isStarted(): bool
    {
        if (!$this->hasProfiler()) {
            return false;
        }

        return \Tideways\Profiler::isStarted();
    }
    private function start(array $options = []): self
    {
        if (!$this->hasProfiler() || !$this->isStarted()) {
            return $this;
        }

        \Tideways\Profiler::start($options);

        return $this;
    }
    private function hasProfiler(): bool
    {
        return class_exists('\Tideways\Profiler');
    }
}
