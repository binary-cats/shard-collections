<?php

use Illuminate\Support\Collection;

it('provides shard macro', function () {
    expect(Collection::hasMacro('shard'))->toBeTrue();
});

it('can shard a plain collection', function () {
    $collection = collect([1, 2, 3]);

    $map = [
        fn ($item) => $item > 10,
        fn ($item) => $item < 2,
    ];

    $output = $collection->shard($map);

    expect($output)->toBeInstanceOf(Collection::class);
    expect($output)->toHaveCount(3);
    expect($output->get(0))->toEqual(collect([]));
    expect($output->get(1))->toEqual(collect([1]));
    expect($output->get(2))->toEqual(collect([2, 3]));
});

it('can shard a plain collection and preserve original collection  keys', function () {
    $collection = collect([1, 2, 3]);

    $map = [
        fn ($item) => $item > 10,
        fn ($item) => $item < 2,
    ];
    $output = $collection->shard($map, true);

    expect($output)->toBeInstanceOf(Collection::class);
    expect($output->get(0))->toEqual(collect([]));
    expect($output->get(1))->toEqual(collect([0 => 1]));
    expect($output->get(2))->toEqual(collect([1 => 2, 2 => 3]));
});

it('can shard an assoc collection', function () {
    $collection = collect(['one' => 1, 'two' => 2, 'three' => 3]);

    /* the order is important for the iterator, so it is important for the iteration */
    $map = [
        fn ($item) => $item < 2,
        fn ($item) => $item > 10,
    ];

    $output = $collection->shard($map);

    expect($output)->toBeInstanceOf(Collection::class);
    expect($output)->toHaveCount(3);
    expect($output->get(0))->toEqual(collect([1]));
    expect($output->get(1))->toEqual(collect([]));
    expect($output->get(2))->toEqual(collect([2, 3]));
});

it('can shard an assoc collection and preserve keys', function () {
    $collection = collect(['one' => 1, 'two' => 2, 'three' => 3]);

    $map = [
        fn ($item) => $item < 2,
        fn ($item) => $item > 10,
    ];

    $output = $collection->shard($map, true);

    expect($output)->toBeInstanceOf(Collection::class);
    expect($output)->toHaveCount(3);
    expect($output->get(0))->toEqual(collect(['one' => 1]));
    expect($output->get(1))->toEqual(collect([]));
    expect($output->get(2))->toEqual(collect(['two' => 2, 'three' => 3]));
});

it('can shard to the same collection with an empty map', function () {
    $collection = collect([1, 2, 3]);

    $output = $collection->shard([], false, true);

    expect($output)->toBeInstanceOf(Collection::class);
    expect($output)->toHaveCount(1);
    expect($output->first())->toEqual($collection);
});

it('can shard to the same assoc collection with an empty map', function () {
    $collection = collect(['one' => 1, 'two' => 2, 'three' => 3]);

    $output = $collection->shard([], true, true);

    expect($output)->toBeInstanceOf(Collection::class);
    expect($output)->toHaveCount(1);
    expect($output->first())->toEqual($collection);
});

it('can shard collection with forcing remainder', function () {
    $collection = collect(['one' => 1, 'two' => 2, 'three' => 3]);

    $output = $collection->shard(
        map: [fn ($item) => $item < 10],
        forceRemainder: false
    );

    expect($output)->toBeInstanceOf(Collection::class);
    expect($output)->toHaveCount(1);

    $output = $collection->shard(
        map: [fn ($item) => $item < 10],
        forceRemainder: true
    );

    expect($output)->toBeInstanceOf(Collection::class);
    expect($output)->toHaveCount(2);
});
