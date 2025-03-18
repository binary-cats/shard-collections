<?php

namespace BinaryCats\ShardCollections\Macros;

use Closure;
use Illuminate\Support\Collection;

/**
 * Shard the collection into smaller collections using partition callbacks.

 *
 * @mixin \Illuminate\Support\Collection
 */
class Shard
{
    public function __invoke(): Closure
    {
        return function (iterable $map, bool $preserveKeys = false) {
            return $this->pipe(function (Collection $collection) use ($map, $preserveKeys) {
                // Start a new collection
                $results = collect([]);
                // iterate
                foreach ($map as $closure) {
                    // partition the original collection and rewrite the value
                    [$partition, $collection] = $collection->partition(fn ($item) => $closure($item));
                    // Append
                    $results->push($partition->unless($preserveKeys, fn ($all) => $all->values()));
                }
                // append remainder
                $results->push($collection->unless($preserveKeys, fn ($all) => $all->values()));

                // done!
                return $results;
            });
        };
    }
}
