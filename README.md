![](https://banners.beyondco.de/Shard%20Collections.png?theme=light&packageManager=composer+require&packageName=binary-cats%2Fshard-collections&pattern=architect&style=style_1&description=Shard+your+Laravel+collections+into+multiple+sub-collections+based+on+custom+conditions&md=1&showWatermark=1&fontSize=100px&images=sun)

# Laravel Shard Collections

A Laravel package that provides powerful collection macros for sharding collections into multiple sub-collections based on custom conditions.

## Installation

You can install the package via composer:

```bash
composer require binary-cats/shard-collections
```

## Usage

This package provides two collection macros: `shard()` and `shardWithKeys()`. Both allow you to split a collection into multiple sub-collections based on custom conditions.

### The `shard()` Method

The `shard()` method splits a collection into multiple sub-collections based on a series of conditions. Each condition is evaluated in order, and items that match a condition are moved to the corresponding sub-collection. Any remaining items are placed in the final sub-collection.

```php
use Illuminate\Support\Collection;

$collection = collect([1, 2, 3]);

$map = [
    fn ($item) => $item > 10,  // First condition
    fn ($item) => $item < 2,   // Second condition
];

$output = $collection->shard($map);

// Result:
// [
//     collect([]),           // Items > 10 (none)
//     collect([1]),          // Items < 2
//     collect([2, 3])        // Remaining items
// ]
```

You can also preserve the original keys by passing `true` as the second parameter:

```php
$output = $collection->shard($map, true);

// Result:
// [
//     collect([]),           // Items > 10 (none)
//     collect([0 => 1]),     // Items < 2 (with original keys)
//     collect([1 => 2, 2 => 3]) // Remaining items (with original keys)
// ]
```

The remainder is included when it is not empty. You can force remainder by passing the last argument
```php

$output = $collection->shard(
    map: $map, 
    forceRemainder: true
);

// Result:
// [
//     collect(['one' => 1, 'two' => 2, 'three' => 3]), // Remaining items
//     collect([]) // Remainder is empty, but is included 
// ]
```

### The `shardWithKeys()` Method

The `shardWithKeys()` method works similarly to `shard()` but allows you to specify custom keys for each sub-collection. This is particularly useful when you want to give meaningful names to your sharded collections.

```php
$collection = collect([1, 2, 3]);

$map = [
    'high' => fn ($item) => $item > 10,  // First condition
    'low' => fn ($item) => $item < 2,    // Second condition
];

$output = $collection->shardWithKeys($map, 'medium');

// Result:
// [
//     'high' => collect([]),    // Items > 10 (none)
//     'low' => collect([1]),     // Items < 2
//     'medium' => collect([2, 3]) // Remaining items
// ]
```

You can also preserve the original keys by passing `true` as the third parameter:

```php
$output = $collection->shardWithKeys($map, 'medium', true);

// Result:
// [
//     'high' => collect([]),           // Items > 10 (none)
//     'low' => collect([0 => 1]),      // Items < 2 (with original keys)
//     'medium' => collect([1 => 2, 2 => 3]) // Remaining items (with original keys)
// ]
```

## Working with Associative Collections

Both methods work seamlessly with associative collections:

```php
$collection = collect(['one' => 1, 'two' => 2, 'three' => 3]);

$map = [
    'small' => fn ($item) => $item < 2,
    'large' => fn ($item) => $item > 10,
];

$output = $collection->shardWithKeys($map, 'medium', true);

// Result:
// [
//     'small' => collect(['one' => 1]),
//     'large' => collect([]),
//     'medium' => collect(['two' => 2, 'three' => 3])
// ]
```

The remainder is included when it is not empty. You can force remainder by passing the last argument
```php
collect(['one' => 1, 'two' => 2, 'three' => 3])->shard(
    map: [fn ($item) => $item < 10],
    forceRemainder: true
);

// Result:
// [
//     collect(['one' => 1, 'two' => 2, 'three' => 3]) // Remaining items
//     collect([]) // Remainder is empty, but is included
// ]
```


## Empty Maps

When you provide an empty map, both methods will return a single collection containing all items:

```php
$collection = collect([1, 2, 3]);

$output = $collection->shard([]);
// Result: [collect([1, 2, 3])]

$output = $collection->shardWithKeys([], 'all');
// Result: ['all' => collect([1, 2, 3])] 
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
