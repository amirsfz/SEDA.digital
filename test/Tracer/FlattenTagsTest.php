<?php

declare(strict_types=1);

namespace SEDA\TracingTest\Tracer;

use PHPUnit\Framework\TestCase;
use SEDA\Tracing\Tracer\FlattenTags;

class FlattenTagsTest extends TestCase
{
    use FlattenTags;

    public function test_Flattening_Tags_Works_As_Expected(): void
    {
        $tags = [
            'simple' => 'value',
            'some' => [
                'nested' => 'value'
            ],
            'another' => [
                'value-a', 'value-b'
            ],
            'deep' => [
                'nesting' => [
                    'data' => 'value',
                ],
                'simple-array' => ['a', 'b', 'c'],
                'simple' => 'value',
            ]
        ];
        $expected = [
            'simple' => 'value',
            'some.nested' => 'value',
            'another' => 'value-a, value-b',
            'deep.nesting.data' => 'value',
            'deep.simple-array' => 'a, b, c',
            'deep.simple' => 'value',
        ];

        $this->assertSame($expected, $this->getFlattenedTags($tags));
    }
}
