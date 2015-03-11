<?php

	namespace Corelib\Utils
	{
		class ClassUtils
		{
			public static function implementsInterface($class_name, $interface)
			{
				return (array_key_exists($interface, class_implements($class_name)));
			}

			public static function getClassName($obj)
			{
				if (!is_object($obj)
					&& !is_string($obj))
				{
					return (false);
				}

				$fullClassName = is_object($obj) ? get_class($obj) : $obj;
				$className = explode('\\', $fullClassName);
				return (array_pop($className));
			}
		}
	}