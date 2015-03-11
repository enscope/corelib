<?php

	namespace Corelib\ZF2\Base
	{
		use Zend\EventManager\EventInterface;

		abstract class AbstractModuleBase
		extends AbstractModule
		{
			private $_dir;
			private $_namespace;

			protected function __construct($dir, $namespace)
			{
				$this->_dir = $dir;
				$this->_namespace = $namespace;
			}

			public function onBootstrap(EventInterface $e)
			{
				// empty
			}

			public function getConfig()
			{
				return include $this->_dir . '/config/module.config.php';
			}

			public function getAutoloaderConfig()
			{
				return [
					'Zend\Loader\StandardAutoloader' => [
						'namespaces' => [
							$this->_namespace => ($this->_dir . '/src'),
						]
					]
				];
			}
		}
	}