<?php

	namespace Corelib\Utils
	{
		use Exception;

		class UrlParser
		{
			private $parsed = null;

			public function __construct($url)
			{
				if (!$this->parsed = parse_url($url))
				{
					throw new Exception("Invalid URL given.");
				}
			}

			public function getScheme()
			{
				return ($this->getUrlComponent('scheme'));
			}

			public function getHost()
			{
				return ($this->getUrlComponent('host'));
			}

			public function getPath()
			{
				return ($this->getUrlComponent('path'));
			}

			public function getPort()
			{
				return ($this->getUrlComponent('port'));
			}

			public function getUser()
			{
				return ($this->getUrlComponent('user'));
			}

			public function getPassword()
			{
				return ($this->getUrlComponent('pass'));
			}

			public function getQuery()
			{
				return ($this->getUrlComponent('query'));
			}

			public function getFragment()
			{
				return ($this->getUrlComponent('fragment'));
			}

			public function getUrlComponent($component, $default_value = null)
			{
				return ($this->parsed[$component] ?: $default_value);
			}
		}
	}