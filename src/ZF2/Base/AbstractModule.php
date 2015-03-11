<?php

	namespace Corelib\ZF2\Base
	{
		use Zend\EventManager\EventInterface;
		use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
		use Zend\ModuleManager\Feature\BootstrapListenerInterface;
		use Zend\ModuleManager\Feature\ConfigProviderInterface;

		abstract class AbstractModule
		implements
			AutoloaderProviderInterface,
			ConfigProviderInterface,
			BootstrapListenerInterface
		{
			abstract public function onBootstrap(EventInterface $e);
			abstract public function getConfig();
			abstract public function getAutoloaderConfig();
		}
	}