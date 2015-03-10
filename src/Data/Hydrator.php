<?php

	namespace Corelib\Data
	{
		use Corelib\Cli\Console;
		use Corelib\Utils\ClassUtils;

		class Hydrator
		{
			private static $instance = null;

			public static function sharedHydrator()
			{
				if (!(self::$instance instanceof Hydrator))
				{
					self::$instance = new Hydrator();
				}
				return (self::$instance);
			}

			public function fromArray(Hydratable &$obj, $values, $map_id = null, $hints_only = false)
			{
				$this->assertHydratable($obj);
				$hints = $obj->__fromArray($map_id) ?: [];

				if (is_array($values))
				{
					foreach ($values as $key => $value)
					{
						if (!$hints_only
						    || array_key_exists($key, $hints))
						{
							try
							{
								$target = $key;
								$targetClass = null;
								if (array_key_exists($key, $hints))
								{
									if (is_array($hints[$key]))
									{
										if (!empty($hints[$key]['property']))
										{
											$target = $hints[$key]['property'];
										}
										if (!empty($hints[$key]['class']))
										{
											$targetClass = $hints[$key]['class'];
										}
									}
									else
									{
										$target = $hints[$key];
									}
								}

								if ($targetClass !== null)
								{
									$value = $this->fromArray(new $targetClass(), $value, $map_id, $hints_only);
								}
								$this->setProperty($obj, $target, $value);
							}
							catch (HydratorException $hex)
							{
								Console::warn($hex->getMessage());
								continue;
							}
						}
					}
				}

				return ($obj);
			}

			public function toArray(Hydratable &$obj, $map_id = null, $hints_only = false)
			{
				$this->assertHydratable($obj);
				$hints = $obj->__toArray($map_id) ?: [];

				if (!$hints_only)
				{
					$properties = [];
					foreach (get_class_methods($obj) as $method)
					{
						if ($propertyName = $this->findPropertyName($method, ['get', 'is']))
						{
							$properties[$propertyName] = $propertyName;
						}
					}

					$hints = array_merge($properties, $hints);
				}

				$array = [];
				foreach ($hints as $p_name => $p_hint)
				{
					try
					{
						$value = $this->getProperty($obj, $p_hint);
						if (is_object($value))
						{
							if (!($value instanceof Hydratable))
							{
								$valueClass = get_class($value);
								throw new HydratorException("Hydrated class {$valueClass} must implement Corelib\\Data\\Hydratable interface.");
							}
							$value = $value->toArray();
						}
						$array[$p_hint] = $value;
					}
					catch (HydratorException $hex)
					{
						Console::warn($hex->getMessage());
						continue;
					}
				}

				return ($array);
			}

			private function assertHydratable($obj)
			{
				if (!($obj instanceof Hydratable))
				{
					$objClass = get_class($obj);
					throw new HydratorException("Hydrated class {$objClass} must implement Corelib\\Data\\Hydratable interface.");
				}
			}

			private function findPropertyName($method, $prefixes)
			{
				foreach ($prefixes as $prefix)
				{
					if ((substr($method, 0, strlen($prefix)) == $prefix)
					    && ctype_upper($method[strlen($prefix)]))
					{
						return (lcfirst(substr($method, strlen($prefix))));
					}
				}
				return (false);
			}

			protected function getProperty(&$obj, $property)
			{
				if ($getter = $this->findAccessor($obj, $property, ['get', 'is']))
				{
					return ($obj->$getter());
				}
				throw new HydratorException(sprintf("No getter for property '%s' in class '%s'.", $property, get_class($obj)));
			}

			protected function setProperty(&$obj, $property, $value)
			{
				if ($setter = $this->findAccessor($obj, $property, 'set'))
				{
					$obj->$setter($value);
					return (true);
				}
				throw new HydratorException(sprintf("No setter for property '%s' in class '%s'.", $property, get_class($obj)));
			}

			private function findAccessor(&$obj, $property, $types)
			{
				foreach ((!is_array($types) ? [$types] : $types) as $type)
				{
					foreach ($this->guessAccessorNames($type, $property) as $accessorName)
					{
						if (method_exists($obj, $accessorName))
						{
							return ($accessorName);
						}
					}
				}
				return (false);
			}

			private function guessAccessorNames($type, $property)
			{
				$names = [];
				$property = str_replace('-', '_', $property);
				if (strpos($property, '_') !== false)
				{
					$names[] = $type . implode('', array_map(function($i) {
							return (ucfirst($i));
						}, explode('_', $property)));
				}
				$names[] = $type . ucfirst($property);
				return ($names);
			}
		}
	}