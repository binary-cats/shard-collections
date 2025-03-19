<?php

use Illuminate\Support\Collection;

it('provides shard macro', function () {
    expect(Collection::hasMacro('shard'))->toBeTrue();
});

it('can shard a plain collection into collections with custom keys', function () {
    $collection = collect([1, 2, 3]);

    $map = [
        'Foo' => fn ($item) => $item > 10,
        'Bar' => fn ($item) => $item < 2,
    ];

    $output = $collection->shardWithKeys($map, 'Baz');

    expect($output)->toBeInstanceOf(Collection::class);
    expect($output)->toHaveCount(3);
    expect($output->get('Foo'))->toEqual(collect([]));
    expect($output->get('Bar'))->toEqual(collect([1]));
    expect($output->get('Baz'))->toEqual(collect([2, 3]));
});

it('can shard a plain collection into collections with custom keys and preserve original collection  keys', function () {
    $collection = collect([1, 2, 3]);

    $map = [
        'Foo' => fn ($item) => $item > 10,
        'Bar' => fn ($item) => $item < 2,
    ];
    $output = $collection->shardWithKeys($map, 'Baz', true);

    expect($output)->toBeInstanceOf(Collection::class);
    expect($output->get('Foo'))->toEqual(collect([]));
    expect($output->get('Bar'))->toEqual(collect([0 => 1]));
    expect($output->get('Baz'))->toEqual(collect([1 => 2, 2 => 3]));
});

it('can shard an assoc collection into collections with custom keys', function () {
    $collection = collect(['one' => 1, 'two' => 2, 'three' => 3]);

    /* the order is important for the iterator, so it is important for the iteration */
    $map = [
        'Foo' => fn ($item) => $item < 2,
        'Bar' => fn ($item) => $item > 10,
    ];

    $output = $collection->shardWithKeys($map, 'Baz');

    expect($output)->toBeInstanceOf(Collection::class);
    expect($output)->toHaveCount(3);
    expect($output->get('Foo'))->toEqual(collect([1]));
    expect($output->get('Bar'))->toEqual(collect([]));
    expect($output->get('Baz'))->toEqual(collect([2, 3]));
});

it('can shard an assoc collection into collections with custom keys and preserve keys', function () {
    $collection = collect(['one' => 1, 'two' => 2, 'three' => 3]);

    $map = [
        'Foo' => fn ($item) => $item < 2,
        'Bar' => fn ($item) => $item > 10,
    ];

    $output = $collection->shardWithKeys($map, 'Baz', true);

    expect($output)->toBeInstanceOf(Collection::class);
    expect($output)->toHaveCount(3);
    expect($output->get('Foo'))->toEqual(collect(['one' => 1]));
    expect($output->get('Bar'))->toEqual(collect([]));
    expect($output->get('Baz'))->toEqual(collect(['two' => 2, 'three' => 3]));
});

it('can shard to the same collection with an empty map', function () {
    $collection = collect([1, 2, 3]);

    $output = $collection->shardWithKeys([], 'Baz');

    expect($output)->toBeInstanceOf(Collection::class);
    expect($output)->toHaveCount(1);
    expect($output->get('Baz'))->toEqual($collection);
});

it('can shard to the same assoc collection with an empty map', function () {
    $collection = collect(['one' => 1, 'two' => 2, 'three' => 3]);

    $output = $collection->shardWithKeys([], 'Baz', true);

    expect($output)->toBeInstanceOf(Collection::class);
    expect($output)->toHaveCount(1);
    expect($output->get('Baz'))->toEqual($collection);
});

it('can shard collection with forcing remainder', function () {
    $collection = collect(['one' => 1, 'two' => 2, 'three' => 3]);

    $output = $collection->shardWithKeys(
        map: [fn ($item) => $item < 10],
        remainderKey: 'Baz',
        forceRemainder: false
    );

    expect($output)->toBeInstanceOf(Collection::class);
    expect($output)->toHaveCount(1);

    $output = $collection->shardWithKeys(
        map: [fn ($item) => $item < 10],
        remainderKey: 'Baz',
        forceRemainder: true
    );

    expect($output)->toBeInstanceOf(Collection::class);
    expect($output)->toHaveCount(2);
});
