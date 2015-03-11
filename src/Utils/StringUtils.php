<?php

	namespace Corelib\Utils
	{
		class StringUtils
		{
			public static function startsWith($string, $prefix)
			{
				return (substr($string, 0, strlen($prefix)) === $prefix);
			}

			public static function endsWith($string, $suffix)
			{
				return (substr($string, -strlen($suffix) == $suffix));
			}

			public static function startsEndsWith($string, $prefix, $suffix)
			{
				return (self::startsWith($string, $prefix)
						&& self::endsWith($string, $suffix));
			}
		}
	}