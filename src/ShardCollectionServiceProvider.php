<?php

namespace BinaryCats\ShardCollections;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class ShardCollectionServiceProvider extends ServiceProvider
{
    /**
     * Register collection macros
     */
    public function register(): void
    {
        Collection::make($this->macros())
            ->reject(fn ($class, $macro) => Collection::hasMacro($macro))
            ->each(fn ($class, $macro) => Collection::macro($macro, app($class)()));
    }

    private function macros(): array
    {
        return [
            'shard' => \BinaryCats\ShardCollections\Macros\Shard::class,
            // 'shardWithKeys' => \BinaryCats\ShardCollections\Macros\Shard::class,
        ];
    }
}
