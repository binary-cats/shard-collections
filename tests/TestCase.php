<?php

namespace BinaryCats\ShardCollections\Tests;

use BinaryCats\ShardCollections\ShardCollectionServiceProvider;
use PHPUnit\Framework\TestCase as BaseTestCase;
use ReflectionClass;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        $this->createDummyProvider()->register();
    }

    protected function createDummyProvider(): ShardCollectionServiceProvider
    {
        $reflectionClass = new ReflectionClass(ShardCollectionServiceProvider::class);

        return $reflectionClass->newInstanceWithoutConstructor();
    }
}
