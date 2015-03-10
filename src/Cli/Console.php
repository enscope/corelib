<?php

    namespace Corelib\Cli
    {
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
         */
        class Console
        {
	        const NONE = 0;
            const FATAL = 1;
            const ERROR = 2;
            const WARNING = 3;
            const INFO = 4;
            const DEBUG = 5;
            const TRACE = 6;

            private static $levels = [
	            'none' => self::NONE, // special level, that ensures, the string is ALWAYS displayed
                'fatal' => self::FATAL, 'error' => self::ERROR, 'warn' => self::WARNING,
                'info' => self::INFO, 'debug' => self::DEBUG, 'trace' => self::TRACE
            ];

            /**
             * @var int
             */
            private static $verbosity = self::FATAL;

            /**
             * Prints text to standard output with specified verbosity.
             * Method only prints something, when PHP is running in CLI mode.
             *
             * @param string $text  Message to be printed
             * @param int $level    Verbosity level of the message
             */
            public static function log($text, $level)
            {
                if ((PHP_SAPI == 'cli')
                    && (self::$verbosity >= max(self::NONE, $level)))
                {
                    $time = date('H:i:s');
                    \cli\err(sprintf('[%s] (%d) %s', $time, $level, $text));
                }
            }

            public static function __callStatic($name, $args)
            {
                if (count($args)
                    && array_key_exists(strtolower($name), self::$levels))
                {
                    self::log(vsprintf(array_shift($args), $args), self::$levels[$name]);
                }
            }

            /**
             * @param int $verbosity
             */
            public static function setVerbosity($verbosity)
            {
                self::$verbosity = max(self::NONE, $verbosity);
                self::info("Console verbosity level set to {$verbosity}");
            }

	        /**
	         * @return int
	         */
	        public static function getVerbosity()
	        {
		        return self::$verbosity;
	        }
        }
    }