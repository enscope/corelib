<?php

	namespace Corelib\Cli
	{
		use Corelib\Utils\StringUtils;

		/**
		 * All static verbosity-level shorthands behave like printf().
		 *
		 * @method static void none(string $text)
		 * @method static void fatal(string $text)
		 * @method static void error(string $text)
		 * @method static void warn(string $text)
		 * @method static void info(string $text)
		 * @method static void debug(string $text)
		 * @method static void trace(string $text)
		 *
		 * @method static boolean isNoneEnabled()
		 * @method static boolean isFatalEnabled()
		 * @method static boolean isErrorEnabled()
		 * @method static boolean isWarnEnabled()
		 * @method static boolean isInfoEnabled()
		 * @method static boolean isDebugEnabled()
		 * @method static boolean isTraceEnabled()
		 */
		class Console
		{
			const NONE    = 0;
			const FATAL   = 1;
			const ERROR   = 2;
			const WARNING = 3;
			const INFO    = 4;
			const DEBUG   = 5;
			const TRACE   = 6;

			const STREAM_ERROR = 'err';
			const STREAM_OUT   = 'out';

			private static $levels = [
				'none'  => self::NONE, // special level, that ensures, the string is ALWAYS displayed
				'fatal' => self::FATAL,
				'error' => self::ERROR,
				'warn'  => self::WARNING,
				'info'  => self::INFO,
				'debug' => self::DEBUG,
				'trace' => self::TRACE
			];

			private static $levelStream = [
				self::NONE    => self::STREAM_ERROR,
				self::FATAL   => self::STREAM_ERROR,
				self::ERROR   => self::STREAM_ERROR,
				self::WARNING => self::STREAM_ERROR,
				self::INFO    => self::STREAM_OUT,
				self::DEBUG   => self::STREAM_OUT,
				self::TRACE   => self::STREAM_OUT
			];

			/**
			 * @var int
			 */
			private static $verbosity = self::ERROR;

			public static function __callStatic($name, $args)
			{
				if (count($args))
				{
					$level = strtolower($name);
					if (array_key_exists($level, self::$levels))
					{
						self::log(vsprintf(array_shift($args), $args), self::$levels[$level]);

						return (null);
					}

				}
				elseif (StringUtils::startsEndsWith($name, 'is', 'enabled'))
				{
					$level = strtolower(substr($name, 2, strlen($name) - 9));
					if (array_key_exists($level, self::$levels))
					{
						return (self::isLevelEnabled(self::$levels[$level]));
					}
				}

				return (null);
			}

			/**
			 * Prints text to standard output with specified verbosity.
			 * Method only prints something, when PHP is running in CLI mode.
			 *
			 * @param string $text Message to be printed
			 * @param int $level Verbosity level of the message
			 */
			public static function log($text, $level)
			{
				if (self::isLevelEnabled($level))
				{
					$time = date('H:i:s');
					self::logToStream(sprintf("[%s] (%d) %s\n", $time, $level, $text), $level);
				}
			}

			/**
			 * Checks, if specified level will produce some output.
			 *
			 * @param int $level Verbosity level
			 *
			 * @return bool True, if there will be some output for this level
			 */
			public static function isLevelEnabled($level)
			{
				return ((PHP_SAPI == 'cli')
					&& (self::$verbosity >= max(self::NONE, $level)));
			}

			private static function logToStream($text, $level)
			{
				if (PHP_SAPI == 'cli')
				{
					$stream = array_key_exists($level, self::$levelStream)
						? self::$levelStream[$level]
						: self::STREAM_OUT;
					switch ($stream)
					{
						case self::STREAM_ERROR:
							fwrite(STDERR, $text);
							break;
						case self::STREAM_OUT:
							fwrite(STDOUT, $text);
							break;
					}
				}
				else
				{
					// if invoked outside of CLI environment,
					// use default PHP logging mechanism
					error_log($text);
				}
			}

			/**
			 * @return int
			 */
			public static function getVerbosity()
			{
				return self::$verbosity;
			}

			/**
			 * @param int $verbosity
			 */
			public static function setVerbosity($verbosity)
			{
				self::$verbosity = max(self::NONE, $verbosity);
				self::info("Console verbosity level set to {$verbosity}");
			}
		}
	}