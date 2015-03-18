<?php

	namespace Corelib\FileSystem
	{
		use Corelib\Core\DisposableInterface;
		use Corelib\Core\Using;
		use Exception;

		class Directory
		implements DisposableInterface
		{
			private $_dir;

			public function __construct($dir_name, $context = null)
			{
				if (is_null($context))
				{
					$this->_dir = dir($dir_name);
				}
				else
				{
					$this->_dir = dir($dir_name, $context);
				}
			}

			public function __destruct()
			{
				$this->dispose();
			}

			public function read()
			{
				$this->assertHandle();
				return ($this->_dir->read());
			}

			public static function each($path, $callable)
			{
				if (!is_callable($callable))
				{
					throw new FileSystemException("Directory::each() expects callable.");
				}

				return (Using::_(new Directory($path),
					function (Directory $dir) use ($path, $callable)
					{
						$result = [];
						while ($entry = $dir->read())
						{
							if ($entry[0] == '.')
							{
								continue;
							}
							if (($partial = $callable($path . DIRECTORY_SEPARATOR . $entry)) !== null)
							{
								$result[] = $partial;
							}
						}
						return ($result);
					}));
			}

			public function rewind()
			{
				$this->assertHandle();
				$this->_dir->rewind();
			}

			public function close()
			{
				if ($this->isHandle())
				{
					$this->_dir->close();
					$this->_dir = null;
				}
			}

			public function isHandle()
			{
				return (!is_null($this->_dir));
			}

			private function assertHandle()
			{
				if (!$this->isHandle())
				{
					throw new FileSystemException("Directory handle was already closed.");
				}
			}

			/**
			 * Called, when the instance is no longer needed
			 * (e.g. at the end of using() block).
			 *
			 * @throws Exception
			 */
			public function dispose()
			{
				$this->close();
			}
		}
	}