<?php

	namespace Corelib\Cli
	{
		class Argument
		{
			const VALUE_STRING = 'string';
			const VALUE_NUMBER = 'number';

			/** @var string Name of the argument */
			private $_name;
			/** @var string[] String array of options */
			private $_options = [];
			/** @var null|mixed Value of the option after parsing */
			private $_value = null;
			/** @var null|mixed Default value of the option, returned when none specified */
			private $_defaultValue = null;
			/** @var string Type of the value ('string', 'number') */
			private $_valueType = null;
			/** @var string|null Name of the value for usage information */
			private $_valueName = null;
			/** @var bool Flags, if the value is required, once the argument is used */
			private $_requiredValue = false;
			/** @var bool Flags, if the argument is required */
			private $_requiredArgument = false;
			/** @var bool Flags, if multiple values are allowed */
			private $_allowMultiple = false;
			/** @var string Description of the error or NULL if no error */
			private $_error = null;
			/** @var string|null Description of the argument */
			private $_description;

			/**
			 * @param string   $name           Name of the argument in final object
			 * @param string[] $options        Array of names of command line options, where single-character options
			 *                                 are considered short
			 * @param bool     $allow_multiple If set, multiple values are allowed for this argument
			 */
			public function __construct($name, $options, $allow_multiple = false)
			{
				$this->_name = $name;
				$this->_options = $options;
				$this->_allowMultiple = $allow_multiple;
			}

			/**
			 * Sets this argument as required argument.
			 *
			 * @return Argument Fluent interface
			 */
			public function req()
			{
				$this->_requiredArgument = true;

				return ($this);
			}

			/**
			 * Sets value type and requirement flag.
			 *
			 * @param string      $type     Value type ('string', 'number')
			 * @param string|null $name     Name of the value for usage information
			 * @param bool        $required Flags, if value must be specified, if argument is used
			 *
			 * @return Argument Fluent interface
			 */
			public function value($type, $name = null, $required = true)
			{
				$this->_valueType = strtolower($type);
				$this->_requiredValue = $required;
				$this->_valueName = $name;

				return ($this);
			}

			/**
			 * Sets description text for usage information.
			 *
			 * @param string $text Description text
			 *
			 * @return Argument Fluent interface
			 */
			public function desc($text)
			{
				$this->_description = $text;

				return ($this);
			}

			/**
			 * Sets default value parameter.
			 *
			 * @param mixed $value Default value
			 *
			 * @return Argument Fluent interface
			 */
			public function defVal($value)
			{
				$this->_defaultValue = $value;

				return ($this);
			}

			/**
			 * Prepares options for use with getopt() function.
			 *
			 * @param array $short Array reference to short options array
			 * @param array $long  Array reference to long options array
			 *
			 * @return boolean In case, there is some error with options, false is returned
			 */
			public function getopts(&$short, &$long)
			{
				foreach ($this->_options as $option)
				{
					if (strlen($option) == 1)
					{
						$short[] = $this->enrichOption($option);
					}
					else
					{
						$long[] = $this->enrichOption($option);
					}
				}

				return (true);
			}

			private function enrichOption($option)
			{
				if ($this->hasValue())
				{
					return ($option . ($this->_requiredValue ? ':' : '::'));
				}

				return ($option);
			}

			public function hasValue()
			{
				return (null !== $this->_valueType);
			}

			public function usage()
			{
				$uopts = [];
				foreach ($this->_options as $option)
				{
					if (strlen($option) == 1)
					{
						$uopts[] = "-{$option}";
					}
					else
					{
						$opt = "--{$option}";
						if ($this->hasValue())
						{
							$valName = (null !== $this->_valueName) ? $this->_valueName : $this->_valueType;
							$opt .= $this->_requiredValue ? "={$valName}" : "[={$valName}]";
						}
						$uopts[] = $opt;
					}
				}

				return ([$uopts, $this->_description, $this->_defaultValue]);
			}

			public function getValue()
			{
				if ($this->hasValue())
				{
					$result = (null !== $this->_value)
						? $this->_value
						: $this->_defaultValue;
					if ($this->_allowMultiple)
					{
						if (null !== $result)
						{
							// in case of multiple values, method ALWAYS returns array
							$result = is_array($result) ? $result : [$result];
						}
						else
						{
							$result = [];
						}
					}

					return ($result);
				}
				// when does not have value, it is a flag;
				// when flag is set to true, it was set
				return ($this->_value === true);
			}

			public function getError()
			{
				return ($this->_error);
			}

			public function setopts($options)
			{
				foreach ($this->_options as $option)
				{
					if (array_key_exists($option, $options))
					{
						if ($this->hasValue())
						{
							$value = $options[$option];
							if ($this->_requiredValue
								&& (false === $value)
							)
							{
								$this->setError('Value for this argument is required.');
								continue;
							}
							if (!$this->validateValue($value))
							{
								$this->setError("Expected value of type: {$this->_valueType}");
								continue;
							}
							$this->_value = $value;
						}
						else
						{
							$this->_value = true;
						}
					}
				}

				// if value is required and argument still has none associated,
				// it is an error and argument should complain
				if ($this->_requiredArgument
					&& (null === $this->_value)
				)
				{
					$this->setError('This argument is required.');
				}
			}

			private function setError($error, $rewrite = false)
			{
				// error will be only set, if there is no previous error,
				// or when rewrite flag is set
				$this->_error = (!$this->hasError() || $rewrite)
					? $error : $this->_error;
			}

			public function hasError()
			{
				return (null !== $this->_error);
			}

			private function validateValue($value)
			{
				if ($this->hasValue())
				{
					if (is_array($value))
					{
						if (!$this->_allowMultiple)
						{
							$this->setError('Multiple values are not allowed for this argument.');

							return (false);
						}
						foreach ($value as $val)
						{
							if (!$this->validateValue($val))
							{
								return (false);
							}
						}

						return (true);
					}
					else
					{
						switch ($this->_valueType)
						{
							default:
							case self::VALUE_STRING:
								return (is_string($value));

							case self::VALUE_NUMBER:
								return (is_numeric($value));
						}
					}
				}

				// if does not have a value,
				// it should always return false
				$this->setError('Should not assign value to value-less argument.');

				return (false);
			}
		}
	}