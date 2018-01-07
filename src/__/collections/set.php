<?php

namespace collections;

/**
 * Return a new collection with the item set at index to given value.
 * Index can be a path of nested indexes.
 *
 ** __::set(['foo' => ['bar' => 'ter']], 'foo.baz.ber', 'fer');
 ** // → '['foo' => ['bar' => 'ter', 'baz' => ['ber' => 'fer']]]'
 *
 * @param array|object $collection collection of values
 * @param string  $path key or index
 * @param mixed   $value the value to set at position $key
 * @param boolean $strict if the path should be generated even if the path consists of non collections
 * @throws \Exception if the path consists of a non collection and strict is set to false
 *
 * @return array|object the new collection with the item set
 *
 */
function set($collection, $keys, $value = null, $strict = false)
{
    $set_object = function ($object, $key, $value) {
        $newObject = clone $object;
        $newObject->$key = $value;
        return $newObject;
    };
    $set_array = function ($array, $key, $value) {
        $array[$key] = $value;
        return $array;
    };
    $setter = \__::isObject($collection) ? $set_object : $set_array;

    if ($keys === null) {
        return $collection;
    }

    $keys = \explode('.', $keys);
    $key  = \array_shift($keys);

    if (\count($keys) === 0) {
        $collection = call_user_func_array($setter, [$collection, $key, $value]);
    } else {
        $empty = \__::isObject($collection) ? new \stdClass : [];
        if (!\__::has($collection, $key)) {
            $collection = call_user_func_array($setter, [$collection, $key, $empty]);
        } elseif (
            $strict
            && (
                (\__::isObject($collection) && !\__::isObject(\__::get($collection, $key)))
                || (\__::isArray($collection) && !\__::isArray(\__::get($collection, $key)))
            )
        ) {
            $collection = call_user_func_array($setter, [$collection, $key, $empty]);
        } elseif (
            (\__::isObject($collection) && !\__::isObject(\__::get($collection, $key)))
            || (\__::isArray($collection) && !\__::isArray(\__::get($collection, $key)))
        ) {
            throw new \Exception(sprintf('Could not insert value %s into array because the value at key %s is no array.', $value, $key));
        }
        $collection = call_user_func_array(
            $setter,
            [$collection, $key, set(\__::get($collection, $key), implode('.', $keys), $value, $strict)]
        );
    }

    return $collection;
}
