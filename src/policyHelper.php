<?php

use Darkink\AuthorizationServer\Helpers\KeyValuePair;

/**
 * transfrom a string into a regex
 */
function wildcardToRegex(string $src): string
{
    $pattern = $src;
    $pattern = str_replace('*', '.*', $pattern);
    $pattern = str_replace('?', '.', $pattern);

    return "/^$pattern$/i";
}

/**
 * check if a string is null or empty
 */
function isNullOrEmptyString($str)
{
    return ($str === null || trim($str) === '');
}

/**
 * Return a distinct array of object filtered by a callback
 */
function array_distinct(array $src, callable $callable): array
{
    $result = array_map($callable, $src);
    $unique = array_unique($result);
    return array_values(array_intersect_key($src, $unique));
}

/**
 * Flatten the array
 */
function array_flatten(array $src){
    return array_merge(...array_values($src));
}

/**
 * Check if an array contains a least on of callable item
 */
function array_any(array $src, callable $callable): bool {
    return array_count($src, $callable) > 0;
}

/**
 * Count item of array that contains a least on of callable item
 */
function array_count(array $src, callable $callable): bool {
    return count(array_filter($src, $callable));
}

/**
 * Group items of array by a callback and a field selector
 *
 * @return KeyValuePair[]
 * */
function array_group(array $items, callable $callable_group, ?callable $callable_group_key = null): array
{
    $resultsByKey = [];
    $groups = [];

    foreach ($items as $item) {
        $group = $callable_group($item);
        $group_key = $callable_group_key != null ? $callable_group_key($group) : $group;
        if (!array_key_exists($group_key, $groups)) {
            $groups[$group_key] = $group;
            $resultsByKey[$group_key] = [];
        }
        $resultsByKey[$group_key][] = $item;
    }

    $result = [];
    foreach ($groups as $key => $item) {
        $result[$key] = new KeyValuePair($item, $resultsByKey[$key]);
    }

    return $result;
}
