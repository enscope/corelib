<?php

	namespace Corelib\Cli
	{
		use Corelib\Utils\PhpUtils;
		use Exception;

		/**
		 * Class Arguments
		 *
		 * Simple command-line arguments parser, utilizing php getopt() method
		 * to perform actual parsing.
		 *
		 * @package Corelib\Cli
		 */
		class Arguments
			implements \ArrayAccess
		{
			/**
			 * @var Argument[] Arguments to parse from command line
			 */
			private $_args = [];
			/**
			 * @var string[] Array of errors, indexed by argument name
			 */
			private $_errors = [];

			public function __construct()
			{
				if (!PhpUtils::isCli())
				{
					throw new Exception('Arguments parser can only operate in CLI environment.');
				}
			}

			/**
			 * @param string   $name           Name of this argument in final object
			 * @param string[] $options        Options assigned to this argument
			 * @param bool     $allow_multiple Flags, if multiple values for this argument are allowed
			 *
			 * @return Argument Fluent interface to Argument instance
			 */
			public function add($name, $options, $allow_multiple = false)
			{
				return ($this->_args[$name] = new Argument($name, $options, $allow_multiple));
			}

			public function process(
				$show_usage_on_error = true,
				$ignore_errors = [])
			{
				$this->parse();

				if (is_array($ignore_errors))
				{
					foreach ($ignore_errors as $opt)
					{
						// if one of these flags is specified,
						// process always returns true and other processing
						// is in the hands of client code
						if ($this[$opt] === true)
						{
							return (true);
						}
					}
				}

				if ($this->hasErrors())
				{
					$this->displayErrors($show_usage_on_error);
					return (false);
				}

				return (true);
			}

			public function parse()
			{
				$this->resetInstance();

				$short = [];
				$long = [];

				$this->getopts($short, $long);
				$this->setopts(getopt(implode('', $short), $long));
			}

			private function resetInstance()
			{
				$this->_errors = [];
			}

			private function getopts(&$short, &$long)
			{
				foreach ($this->_args as $name => $arg)
				{
					$arg->getopts($short, $long);
				}
			}

			private function setopts($options)
			{
				foreach ($this->_args as $name => $arg)
				{
					$arg->setopts($options);
					if ($arg->hasError())
					{
						// if there was some error, add it to errors array
						$this->_errors[$name] = $arg->getError();
					}
				}
			}

			public function displayErrors($show_usage = true)
			{
				if ($this->hasErrors())
				{
					foreach ($this->getErrors() as $name => $error)
					{
						printf("$name: $error\n");
					}

					if ($show_usage)
					{
						echo "\n";
						$this->displayUsage();
					}
				}
			}

			public function displayUsage()
			{
				global $argv;
				printf("Usage: %s [options]\n\n", $argv[0]);

				$usages = [];
				$maxOptsLength = 0;
				foreach ($this->_args as $arg)
				{
					list($opts, $description, $defaultValue) = $arg->usage();
					$usageOpts = implode(', ', $opts);
					$maxOptsLength = max($maxOptsLength, strlen($usageOpts));
					$usageDesc = (null !== $defaultValue)
						? "$description (default: $defaultValue)"
						: $description;
					$usages[] = [$usageOpts, $usageDesc];
				}

				foreach ($usages as $usage)
				{
					printf("  %-{$maxOptsLength}s\t%s\n", $usage[0], $usage[1]);
				}
				echo "\n";
			}

			public function hasErrors()
			{
				return (count($this->_errors));
			}

			public function getErrors()
			{
				return ($this->_errors);
			}

			public function dump($level = Console::INFO)
			{
				foreach ($this->_args as $name => $arg)
				{
					$value = $arg->getValue();
					Console::log(sprintf("%s = %s", $name, is_array($value)
						? "'" . implode("', '", $value) . "'" : "'$value'"), $level);
				}
			}

			public function offsetExists($offset)
			{
				return ($this->isArgument($offset));
			}

			public function isArgument($name)
			{
				return (array_key_exists($name, $this->_args));
			}

			public function offsetGet($offset)
			{
				return ($this->getArgumentValue($offset));
			}

			//region --- ArrayAccess implementation ---

			public function getArgumentValue($name)
			{
				return (($arg = $this->getArgument($name))
					? $arg->getValue() : null);
			}

			public function getArgument($name)
			{
				return $this->isArgument($name)
					? $this->_args[$name]
					: false;
			}

			public function offsetSet($offset, $value)
			{
				trigger_error('Set operation not supported for Arguments', E_USER_WARNING);
			}

			public function offsetUnset($offset)
			{
				trigger_error('Unset operation not supported for Arguments', E_USER_WARNING);
			}

			//endregion
		}
	}