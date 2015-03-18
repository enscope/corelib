<?php

	namespace Corelib\Cli
	{
		/**
		 * Class MockArguments
		 *
		 * Simple extension to Arguments class, that mocks the data,
		 * usually parsed by getopt(), to allow testing.
		 * Assumes, that getopt() is operating properly.
		 *
		 * @package Corelib\Cli
		 */
		class MockArguments
			extends Arguments
		{
			/** @var string[] Simulated data (result of getopt()) */
			private $_mockInput;

			public function __construct($mock_input = [])
			{
				$this->_mockInput = $mock_input;
			}

			protected function __getopt($short, $long)
			{
				return ($this->_mockInput);
			}
		}
	}