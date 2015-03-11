<?php

	namespace Corelib\Utils
	{
		use BitWasp\BitcoinLib\BIP32;
		use Corelib\Cli\Console;

		class BitcoinUtils
		{
			const SATOSHI_IN_BTC = 100000000.0;

			/**
			 * Returns Bitcoin (BTC) amount of the amount given in Satoshi.
			 *
			 * @param float $satoshi_amount Amount in Satoshi
			 *
			 * @return float Equivalent amount in BTC
			 */
			public static function satoshiToBtc($satoshi_amount)
			{
				return ($satoshi_amount / self::SATOSHI_IN_BTC);
			}

			/**
			 * Returns Satoshi amount of the amount given in BTC, rounding number
			 * HALF_DOWN automatically to return integer.
			 *
			 * @param float   $btc_amount Amount in Bitcoins
			 *
			 * @return int Equivalent amount in Satoshi
			 */
			public static function btcToSatoshi($btc_amount)
			{
				return ((int) round($btc_amount * self::SATOSHI_IN_BTC, 0, PHP_ROUND_HALF_DOWN));
			}

			/**
			 * Method uses XPUB to generate next deterministic address.
			 *
			 * @param string $xpub  Master public key (XPUB)
			 * @param int    $index Index of the deterministic address
			 *
			 * @return string Generated address for specified index
			 */
			public static function generateAddress($xpub, $index = 0)
			{
				if (!class_exists('BitWasp\\BitcoinLib\\BIP32'))
				{
					// if class BIP32 is not available, trigger an error
					trigger_error("Library BitWasp/bitcoin-lib-php is required to generate deterministic address",
						E_USER_ERROR);
				}
				Console::debug("Generating address for XPUB '%s' index %d", $xpub, $index);
				$address = BIP32::build_address($xpub, sprintf('0/%d', $index))[0];
				Console::debug("Generated address (index %d): '%s'", $index, $address);
				return ($address);
			}
		}
	}