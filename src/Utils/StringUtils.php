<?php

	namespace Corelib\Utils
	{
		class StringUtils
		{
			public static function startsWith($string, $prefix)
			{
				return (substr($string, 0, strlen($prefix)) === $prefix);
			}
		}
	}