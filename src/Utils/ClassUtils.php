<?php

	namespace Corelib\Utils
	{
		class ClassUtils
		{
			public static function implementsInterface($class_name, $interface)
			{
				return (array_key_exists($interface, class_implements($class_name)));
			}
		}
	}