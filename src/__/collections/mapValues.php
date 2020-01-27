<?php

namespace collections;

/**
 * @internal
 *
 * @param iterable $iterable
 * @param \Closure $closure
 *
 * @return \Generator
 */
function mapValuesIterable($iterable, $closure)
{
    foreach ($iterable as $key => $value) {
        yield $key => call_user_func_array($closure, [$value, $key, $iterable]);
    }
}

/**
 * Transforms the values in a collection by running each value through the iterator.
 *
 * **Usage**
 *
 * ```php
 * __::mapValues(['x' => 1], function($value, $key, $collection) {
 *     return "{$key}_{$value}";
 * });
 * ```
 *
 * **Result**
 *
 * ```
 * ['x' => 'x_1']
 * ```
 *
 * @param array|iterable $array       Array of values
 * @param \Closure|null  $closure     Closure to map the values
 *
 * @return array|\Generator
 */
function mapValues($array, \Closure $closure = null)
{
    if (is_null($closure)) {
        $closure = '__::identity';
    }

    if (is_array($array)) {
        return iterator_to_array(mapValuesIterable($array, $closure));
    }

    return mapValuesIterable($array, $closure);
}
