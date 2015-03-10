<?php

	namespace Corelib\Remote
	{
		/**
		 * Class CurlResponse
		 *
		 * @method boolean isSuccess()
		 * @method boolean isMovedPermanently()
		 * @method boolean isFound()
		 * @method boolean isBadRequest()
		 * @method boolean isUnauthorized()
		 * @method boolean isForbidden()
		 * @method boolean isNotFound()
		 * @method boolean isServerError()
		 *
		 * @package Corelib\Remote
		 */
		class CurlResponse
		{
			private static $httpStatusCodes = [
				'Success' => 200,
				'MovedPermanently' => 301,
				'Found' => 302,
				'BadRequest' => 400,
				'Unauthorized' => 401,
				'Forbidden' => 403,
				'NotFound' => 404,
				'ServerError' => 500
			];

			/**
			 * @var string[]
			 */
			private $headers = [];
			/**
			 * @var string
			 */
			private $body;

			public function __construct($response, $header_size)
			{
				$headers_str = substr($response, 0, $header_size);
				$this->headers = $this->httpHeadersToArray($headers_str);
				$this->body = substr($response, $header_size);
			}

			public function __call($name, $args)
			{
				$className = __CLASS__;

				if (@substr($name, 0, 2) == 'is')
				{
					$httpCode = substr($name, 2);
					if (array_key_exists($httpCode, self::$httpStatusCodes))
					{
						return (call_user_func([$this, 'isStatusCode'], self::$httpStatusCodes[$httpCode]));
					}
				}

				trigger_error("Call to undefined method $className::$name()", E_USER_ERROR);
			}

			private function httpHeadersToArray($str_headers)
			{
				$result = [];

				foreach (explode("\r\n\r\n", trim($str_headers)) as $raw_header_set)
				{
					$partial = new \stdClass();
					$partial->httpStatus = null;
					$partial->headers = [];

					foreach (explode("\r\n", trim($raw_header_set)) as $idx => $raw_header)
					{
						if ($idx === 0)
						{
							$exploded = explode(' ', $raw_header);
							$partial->httpStatus = [
								'version' => array_shift($exploded),
								'code' => array_shift($exploded),
								'description' => implode(' ', $exploded)
							];
						}
						if (count($parts = explode(': ', $raw_header)) >= 2);
						{
							$partial->headers[strtolower(array_shift($parts))] = implode(': ', $parts);
						}
					}
					$result[] = $partial;
				}

				return ($result);
			}

			//region --- Getters ---

			/**
			 * @return \string[]
			 */
			public function getHttpStatus()
			{
				return $this->headers[count($this->headers) - 1]->httpStatus;
			}

			/**
			 * @return string
			 */
			public function getStatusCode()
			{
				return ($this->getHttpStatus()['code']);
			}

			/**
			 * @param int $code HTTP Status code
			 *
			 * @return bool If HTTP status code equals the one given in argument
			 */
			public function isStatusCode($code)
			{
				return ($this->getStatusCode() == $code);
			}

			/**
			 * @param string $name Name of the header
			 *
			 * @return string|null Header value
			 */
			public function getHeader($name)
			{
				return (@$this->headers[count($this->headers) - 1]->headers[strtolower($name)]);
			}

			/**
			 * @return \string[]|null String-indexed array
			 */
			public function getHeaders()
			{
				return (@$this->headers[count($this->headers) - 1]->headers);
			}

			/**
			 * @return \string[]
			 */
			public function getAllHeaders()
			{
				return ($this->headers);
			}

			/**
			 * @return string
			 */
			public function getBody()
			{
				return $this->body;
			}

			//endregion
		}
	}