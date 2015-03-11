<?php

	namespace Corelib\Data\Pdo
	{
		use Corelib\Cli\Console;
		use Corelib\Data\DataException;
		use Corelib\Data\Hydrator;
		use PDO;

		abstract class AbstractPdoDao
		{
			/**
			 * @var /PDO Database connection
			 */
			protected $pdo;

			public function __construct(PDO $pdo)
			{
				$this->pdo = $pdo;
			}

			/**
			 * Performs select query and, if required, hydrates the response to target class.
			 *
			 * @param string        $query          Query to perform
			 * @param array         $params         Query parameters
			 * @param bool          $single_result  If TRUE, only one result (first) is returned
			 * @param null|string   $hydrate_class  If set, the class will be hydrated from result (using Hydrater)
			 * @param null|string   $hydrate_map_id Identifier of hydration map (or null if none)
			 * @param null|string   $error_message  Message to display, if query fails
			 *
			 * @return mixed Hydrated class, array if no hydration  or FALSE if not found.
			 * @throws DataException
			 */
			protected function select(
				$query, $params = [],
				$single_result = false,
				$hydrate_class = null, $hydrate_map_id = null,
				$error_message = null)
			{
				Console::debug("Performing query: %s", $query);
                $this->logQueryParameters($params, Console::DEBUG);

				$stmt = $this->pdo->prepare($query);
				if (!$stmt
				    || !$stmt->execute($params))
				{
					$this->logDatabaseError($params);
					throw new DataException($error_message ?: "Unable to perform select query.");
				}

				$result = [];
				while ($values = $stmt->fetch(PDO::FETCH_ASSOC))
				{
					$single = $values;
					if (is_string($hydrate_class))
					{
						$single = new $hydrate_class();
						Hydrator::sharedHydrator()->fromArray($single, $values, $hydrate_map_id);
					}
					$result[] = $single;

					if ($single_result)
					{
						// if only one result is requested,
						// break out after first result read
						break;
					}
				}

				// if there is some result, return it;
				// otherwise, return false
				if (count($result))
				{
					return ($single_result ? $result[0] : $result);
				}
				return (false);
			}

			protected function logDatabaseError($params = null, $verbosity = Console::WARNING)
			{
				Console::log(sprintf("Database error info: '%s'", implode('; ', $this->pdo->errorInfo())), $verbosity);
                $this->logQueryParameters($params, $verbosity);
			}

            protected function logQueryParameters($params = null, $verbosity = Console::WARNING)
            {
	            if (Console::isLevelEnabled($verbosity))
	            {
		            if (is_array($params)
		                && count($params))
		            {
			            $logParams = [];
			            foreach ($params as $key => $value)
			            {
				            $logParams[] = sprintf('"%s" => "%s"', $key, $value);
			            }
			            Console::log(sprintf("+ parameters = [%s]", implode(';', $logParams)), $verbosity);
		            }
	            }
            }
		}
	}