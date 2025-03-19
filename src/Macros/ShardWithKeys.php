<?php

namespace BinaryCats\ShardCollections\Macros;

use Closure;
use Illuminate\Support\Collection;

/**
 * Shard the collection into smaller collections using partition callbacks.

 *
 * @mixin \Illuminate\Support\Collection
 */
class ShardWithKeys
{
    public function __invoke(): Closure
    {
        return function (iterable $map, ?string $remainderKey = null, bool $preserveKeys = false, bool $forceRemainder = false) {
            return $this->pipe(function ($collection) use ($map, $remainderKey, $preserveKeys, $forceRemainder) {
                // Start new collection
                $results = collect([]);
                // iterate through the map
                foreach ($map as $key => $closure) {
                    // partition the original collection and rewrite the value
                    [$partition, $collection] = $collection->partition(fn ($item) => $closure($item));
                    // Append
                    $results->put($key, $partition->unless($preserveKeys, fn ($all) => $all->values()));
                }
                // append remainder
                if ($collection->isNotEmpty() || $forceRemainder) {
                    $results->put($remainderKey, $collection->unless($preserveKeys, fn ($all) => $all->values()));
                }

                // done!
                return $results;
            });
        };
    }
}
