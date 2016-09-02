<?php

/**
 * @file
 * Contains \FastFrame\Utility\ArrayHelper
 */

namespace FastFrame\Utility;

/**
 * Utility class for working with arrays
 *
 * @package FastFrame\Utility
 */
class ArrayHelper
{
	/**
	 * Returns whether or not the array is associative
	 *
	 * @see {http://stackoverflow.com/a/5969617/1281788}
	 * @param array $ary
	 * @return bool
	 */
	public static function isAssoc(array &$ary)
	{
		// @formatter:off
		for (reset($ary); is_int(key($ary)); next($ary));
		// @formatter:on

		return is_null(key($ary)) ? false : true;
	}

	/**
	 * Returns the value of the key, or the alt if it doesn't exist
	 *
	 * @param array      $ary
	 * @param string     $key
	 * @param null|mixed $default
	 * @return mixed
	 */
	public static function keyValue(&$ary, $key, $default = null)
	{
		return array_key_exists($key, $ary) ? $ary[$key] : $default;
	}

	/**
	 * Pulls the values from the array with a key given a prefix
	 *
	 * Optionally strips the prefix from the keys
	 *
	 * @param array  $ary
	 * @param string $prefix
	 * @param bool   $stripPrefix
	 * @return array
	 */
	public static function pullPrefix(array &$ary, $prefix, $stripPrefix = false)
	{
		$values = [];
		foreach (array_keys($ary) as $key) {
			if (stripos($key, $prefix) === 0) {
				$values[$stripPrefix ? str_ireplace($prefix, '', $key) : $key] = $ary[$key];
			}
		}

		return $values;
	}

	/**
	 * pushes the values into an array with a key given a prefix
	 *
	 * This is the reverse of array_pull_prefix in that it will set the key to the "{prefix}{key}"
	 *
	 * @param array $ary
	 * @param string $prefix
	 * @return array
	 */
	public static function pushPrefix(array &$ary, $prefix)
	{
		$values = [];
		foreach (array_keys($ary) as $key) {
			$values[$prefix . $key] = $ary[$key];
		}

		return $values;
	}

	/**
	 * Converts the given nodes into an array if needed
	 *
	 * @param $nodes
	 * @return array
	 */
	protected static function convertToArray($nodes)
	{
		return is_array($nodes) ? $nodes : explode('.', $nodes);
	}
}