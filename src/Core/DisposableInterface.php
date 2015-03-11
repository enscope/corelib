<?php

	namespace Corelib\Core
	{
		use Exception;

		interface DisposableInterface
		{
			/**
			 * Called, when the instance is no longer needed
			 * (e.g. at the end of using() block).
			 * @throws Exception
			 */
			public function dispose();
		}
	}