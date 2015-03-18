# Usage of the command-line argument parser

	$args = new Arguments();
	$args->add('input', ['i', 'input'], true)
		 ->desc('Name of the input database')
		 ->value('string', 'file');
	$args->add('main', ['m', 'main'])
		 ->desc('Name of the main database')
		 ->value('string', 'file')
		 ->req();
	$args->add('help', ['h', 'help'])
		 ->desc('Display this usage information');
	$args->add('verbosity', ['v', 'verbosity'])
		 ->desc('Verbosity level (0 - NONE, 6 - TRACE)')
		 ->value('number', '#')
		 ->defVal(3);
	$args->add('version', ['version'])
		 ->desc('Display version information');

	if (!$args->process(true, ['help', 'version']))
	{
		throw new Exception('Invalid arguments.');
	}

And then, accessing the arguments is simple:

	if ($this->_args['version'])
	{
		$this->displayVersion();
		return (false);
	}
	elseif ($this->_args['help'])
	{
		$this->displayVersion();
		echo "\n";
		$this->_args->displayUsage();
		return (false);
	}

	$mainDbFile = $this->_args['main'];
	$inputFiles = $this->_args['input'];
