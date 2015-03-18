<?php

	namespace Corelib\Utils
	{
		class PhpUtils
		{
			public static function isCli()
			{
				return (self::isSapi('cli'));
			}

			public static function isSapi($sapi)
			{
				return (PHP_SAPI == $sapi);
			}
		}
	}