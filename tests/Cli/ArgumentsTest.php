<?php

	namespace Corelib\Cli
	{
		require_once __DIR__ . '/MockArguments.php';

		class ArgumentsTest
			extends \PHPUnit_Framework_TestCase
		{
			public function testArguments_StringType()
			{
				$args = new MockArguments(['arg1' => 'test-string']);
				$args->add('arg1', ['a', 'arg1'], false)
				     ->value('string', '#', true)
				     ->req();
				$args->process(false);

				// string type accepts actually everything
				$this->assertEquals('test-string', $args['arg1']);
			}

			public function testArguments_NumberType()
			{
				$args = new MockArguments(['arg1' => 82517, 'arg2' => 'test-string', 'arg4' => 'test-string']);
				$args->add('arg1', ['arg1'], false)
				     ->value('number', '#', true)
				     ->req();
				$args->add('arg2', ['arg2'], false)
				     ->value('number', '#', true)
				     ->req();
				$args->add('arg3', ['arg3'], false)
				     ->value('number', '#', true)
				     ->defVal(42)
				     ->req();
				$args->add('arg4', ['arg4'], false)
				     ->value('number', '#', true)
				     ->defVal(1024)
				     ->req();
				$args->process(false);

				$this->assertEquals(82517, $args['arg1']);
				$this->assertNull($args['arg2']);
				$this->assertTrue($args->getArgument('arg2')
				                       ->hasError());
				$this->assertEquals(42, $args['arg3']);
				$this->assertTrue($args->getArgument('arg4')
				                       ->hasError());
				$this->assertEquals(1024, $args['arg4']);
			}
		}
	}