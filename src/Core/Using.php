<?php

	namespace Corelib\Core
	{
		use Exception;

		class Using
		{
			/**
			 * Simple Using block, that automatically disposes resources given in argument,
			 * when they implement Disposable interface.
			 *
			 * @return mixed Result of the block execution
			 * @throws Exception
			 */
			public static function _(/** var_args */)
			{
				try
				{
					if (func_num_args() < 2)
					{
						throw new InternalException('Using block must have at least two parameters - resource and block.');
					}

					$args = func_get_args();

					if (!is_callable($block = array_pop($args)))
					{
						throw new InternalException('Last parameter of using block must be callable.');
					}

					$result = null;
					$blockException = null;

					try
					{
						$result = call_user_func_array($block, $args);
					}
					catch (Exception $ex)
					{
						// catch exception here, so dispose() will be properly called
						// for all available resources; exception will be thrown
						// after all resources were disposed
						$blockException = $ex;
					}

					foreach ($args as $resource)
					{
						if ($resource instanceof DisposableInterface)
						{
							$resource->dispose();
						}
					}

					if ($blockException instanceof Exception)
					{
						// throw stored exception now, after all resources were disposed
						// properly; if there was some exception being thrown in dispose()
						// method, this exception is thrown instead of stored exception
						throw $blockException;
					}

					return ($result);
				}
				catch (InternalException $iex)
				{
					trigger_error($iex->getMessage(), E_USER_ERROR);
				}

				return (null);
			}
		}
	}