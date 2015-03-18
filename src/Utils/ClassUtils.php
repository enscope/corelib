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
				if (!($parts = self::getClassNameParts($obj)))
				{
					return (false);
				}
				return (array_pop($parts));
			}

			public static function getNamespace($obj)
			{
				if (!($parts = self::getClassNameParts($obj)))
				{
					return (false);
				}
				return (implode('\\', array_slice($parts, -1)));
			}

			private static function getClassNameParts($obj)
			{
				if (!is_object($obj)
				    && !is_string($obj))
				{
					return (false);
				}

				$fullClassName = is_object($obj) ? get_class($obj) : $obj;
				$parts = explode('\\', $fullClassName);
				return ($parts);
			}
		}
	}