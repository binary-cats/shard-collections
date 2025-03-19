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
        return function (iterable $map, bool $preserveKeys = false, bool $forceRemainder = false) {
            return $this->pipe(function (Collection $collection) use ($map, $preserveKeys, $forceRemainder) {
                // Start a new collection
                $results = collect([]);
                // iterate
                foreach ($map as $closure) {
                    // partition the original collection and rewrite the value
                    [$partition, $collection] = $collection->partition(fn ($item) => $closure($item));
                    // Append
                    $results->push($partition->unless($preserveKeys, fn ($all) => $all->values()));
                }
                // append remainder, if not empty or forced to
                // when the map is empty, collection is matched to the original
                if ($collection->isNotEmpty() || $forceRemainder) {
                    $results->push($collection->unless($preserveKeys, fn ($all) => $all->values()));
                }

                // done!
                return $results;
            });
        };
    }
}
