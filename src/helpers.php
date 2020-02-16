<?php

/**
 * Helper functions derrived from Laravel
 *
 * @repo 
 */

if (!function_exists('value')) {

    /**
     * Checks to see if the value passed is a closure to invoke it. Otherwise it will return the value passed
     * @param mixed|callable $value
     *
     * @return mixed
     */
    function value($value) {
        return $value instanceof Closure ? $value() : $value;
    }

}

if (!function_exists('array_is_accessible')) {

    /**
     * Checks to see if 
     * @param array $value
     *
     * @return bool
     */
    function array_is_accessible($value) {
        return is_array($value) || $value instanceof ArrayAccess;
    }

}

if(!function_exists('array_value_exists')) {

    /**
     * @param array $array
     * @param string $key
     *
     * @return bool
     */
    function array_value_exists($array, $key) {
        if($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

}

if (!function_exists('data_get')) {

    /**
     * @param array $target
     * @param null|string|array $key
     * @param mixed $default
     *
     * @return array|mixed|null
     */
    function data_get($target = array(), $key = null, $default = null)
    {
        if(empty($key)) {
            return $default;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        while(($segment = array_shift($key)) !== null) {
            if(array_is_accessible($target) && array_value_exists($target, $segment)) {
                $target = $target[$segment];
            } elseif(is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }

}

if (!function_exists('data_has')) {
    /**
     * Check if an item or items exist in an array using "dot" notation.
     * @param ArrayAccess|array $array
     * @param string|array $keys
     * @return bool
     */
    function data_has($array, $keys) {
        if(is_null($keys) || !$array) {
            return false;
        }

        $keys = (array) $keys;

        if($keys === array()) {
            return false;
        }

        foreach($keys as $key) {
            $subKeyArray = $array;
            if(array_value_exists($array, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (array_is_accessible($subKeyArray) && array_value_exists($subKeyArray, $segment)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }
        return true;
    }
}

if (!function_exists('data_exists')) {

    /**
     * Alias for array_value_exists with a parameter reversal
     * @param ArrayAccess|array $array
     * @param string $key
     */
    function data_exists($array, $key) {
        return array_value_exists($key, $array);
    }

}


if (!function_exists('pluck_value_from_item_by_key')) {

    /**
     * Creates a closure to use with higher order functions like array_map and array_filter
     * @param string $key
     *
     * @return Closure
     */
    function pluck_value_from_item_by_key($key) {
        return function($item) use ($key) {
            return data_get($item, $key);
        };
    }

}

if (!function_exists('array_to_object')) {

    /**
     * @param array $array
     *
     * @return stdClass
     */
    function array_to_object($array)
    {
        $object = new stdClass();

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = array_to_object($value);
            }
            $object->{$key} = $value;
        }

        return $object;
    }
}

if (!function_exists('arrays_to_object')) {
    /**
     * @param array $arr
     *
     * @return array|stdClass[]
     */
    function arrays_to_object($arr) {
        return array_map('array_to_object', $arr);
    }

}

if (!function_exists('object_to_array')) {

    /**
     * @param $object
     * @return array
     */
    function object_to_array($object) {
        $array = array();
        foreach(get_object_vars($object) as $key => $value) {
            if(is_object($value)) {
                $value = object_to_array($value);
            }

            $array[$key] = $value;
        }
        return $array;
    }

}


if (!function_exists('objects_to_array')) {

    /**
     * @param $objects
     * @return array
     */
    function objects_to_array($objects) {
        return array_map('object_to_array', (array) $objects);
    }

}


if (!function_exists('array_parse_defaults')) {

    /**
     * @param array|mixed $args
     * @param null|string $defaults
     * @return array
     */
    function array_parse_defaults($args, $defaults = null) {
        if(is_object($args)) {
            $r = get_object_vars($args);
        } elseif (is_array($args)) {
            $r =& $args;
        } else {
            parse_str($args, $r);
        }

        if(is_array($defaults)) {
            return array_merge($defaults, $r);
        }

        return $r;
    }

}


if (!function_exists('array_flatten')) {

    /**
     * @param array $data
     * @return array
     */
    function array_flatten($data) {
        $flat = array();

        array_walk_recursive($data, function($value) use (&$flat){
            $flat[] = $value;
        });

        return $flat;
    }

}