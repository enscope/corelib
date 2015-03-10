<?php

	namespace Corelib\Utils
	{
		class GuidUtils
		{
			/**
			 * Simple method to generate valid GUID, that tries to use com_create_guid()
			 * and falls back to custom implementation, if this method is not available.
			 *
			 * NOTE:
			 * Based on code from phunction PHP framework (http://sourceforge.net/projects/phunction/)
			 * as provided on http://php.net/manual/en/function.com-create-guid.php
			 *
			 * @param bool $lowercase When true, resulting string will be transformed
			 *                        to lowercase characters
			 *
			 * @return string Globally unique identifier
			 */
			public static function guid($lowercase = false)
			{
				$guid = (function_exists('com_create_guid') === true)
					? trim(com_create_guid(), '{}')
					: sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
						mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479),
						mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));

				return ($lowercase ? strtolower($guid) : $guid);
			}
		}
	}