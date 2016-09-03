<?php

/**
 * @file
 * Contains \FastFrame\Utility\NestedArrayHelper
 */

namespace FastFrame\Utility;

/**
 * Utility functions for dealing with nested arrays
 *
 * This uses dotted notation (some.where.over.the.rainbow)
 *
 * @package FastFrame\Utility
 */
class NestedArrayHelper
{
	/**
	 * Returns the value at the given path
	 *
	 * Returns the default value if not specified
	 *
	 * @param array        $ary
	 * @param string|array $key
	 * @param null         $alt
	 * @return array|null
	 */
	public static function &get(array &$ary, $key, $alt = null)
	{
		$key = self::convertToArray($key);
		$ref   =& $ary;
		$found = false;
		while (($node = array_shift($key)) !== null) {
			if (is_array($ref) && array_key_exists($node, $ref)) {
				$ref   =& $ref[$node];
				$found = true;
			}
			else {
				$found = false;
				break;
			}
		}

		if ($found) {
			return $ref;
		}
		else {
			return $alt;
		}
	}

	/**
	 * Sets the value at the given path
	 *
	 * @param array        $ary
	 * @param string|array $key
	 * @param mixed        $value
	 */
	public static function set(array &$ary, $key, $value)
	{
		$key = self::convertToArray($key);
		while (($node = array_shift($key)) !== null) {
			if (is_array($ary) && array_key_exists($node, $ary)) {
				if (empty($key)) {
					$ary[$node] = $value;
				}
				else {
					$ary =& $ary[$node];
				}
			}
			elseif (empty($key)) {
				$ary[$node] = $value;
			}
			else {
				$ary[$node] = [];
				$ary        =& $ary[$node];
			}
		}
	}

	/**
	 * Returns whether or not the array has the given key
	 *
	 * If you are going to use the value it's better to use NestedArray::get() instead of this function
	 *
	 * @param array        $ary
	 * @param string|array $key
	 * @return bool
	 */
	public static function has(array &$ary, $key)
	{
		$key = self::convertToArray($key);
		while (($node = array_shift($key)) !== null) {
			if (is_array($ary) && array_key_exists($node, $ary)) {
				$ary =& $ary[$node];
			}
			else {
				return false;
			}
		}

		return true;
	}

	/**
	 * Merges arrays better than array_merge_recursive
	 *
	 * The first array becomes the base for comparison on merge.
	 *
	 * @param array ...$arys
	 * @return array|mixed
	 */
	public static function deepMerge()
	{
		$arys        = func_get_args();
		$prime       = array_shift($arys);
		$primeIsHash = ArrayHelper::isAssoc($prime);

		foreach ($arys as $ary) {
			if (!$primeIsHash && !ArrayHelper::isAssoc($ary)) {
				$prime = array_merge($prime, $ary);
			}
			else {
				$copy = array_diff_key($ary, $prime);
				foreach ($ary as $key => $value) {
					if (array_key_exists($key, $copy)) {
						$prime[$key] = $value;
					}
					elseif (is_array($prime[$key]) || is_array($value)) {
						$prime[$key] = self::deepMerge((array)$prime[$key], (array)$value);
					}
					else {
						$prime[$key] = $value;
					}
				}
			}
		}

		return $prime;
	}

	/**
	 * Expands the array from dotted notation
	 *
	 * @param array $ary
	 * @return array
	 */
	public static function expand(array &$ary)
	{
		$newAry = [];
		foreach ($ary as $key => $value) {
			self::set($newAry, $key, $value);
		}

		return $newAry;
	}

	/**
	 * Compresses the array into dotted notation
	 *
	 * @param array $ary
	 * @return array
	 */
	public static function compress(array &$ary)
	{
		$newAry = [];
		foreach ($ary as $k1 => $v1) {
			if (is_array($v1)) {
				foreach (self::compress($v1) as $k2 => $v2) {
					$newAry["$k1.$k2"] = $v2;
				}
			}
			else {
				self::set($newAry, $k1, $v1);
			}
		}

		return $newAry;
	}

	/**
	 * Converts the given nodes into an array if needed
	 *
	 * @param string|array $key
	 * @return array
	 */
	protected static function convertToArray($key)
	{
		return is_array($key) ? $key : explode('.', $key);
	}
}