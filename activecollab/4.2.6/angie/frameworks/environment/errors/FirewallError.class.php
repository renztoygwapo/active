<?php

	/**
	 * Throw firewall error
	 *
	 * @package angie.frameworks.authentication
	 * @subpackage models
	 */
	class FirewallError extends Error {

		// Known firewall errors
		const UNKNOWN_ERROR           = 0;
		const USER_FAILED_LOGIN       = 1;
		const TOO_MANY_ATTEMPTS       = 2;
		const FIXED_RULE              = 3;
		const INVALID_ADDRESS_FORMAT  = 4;
		const NETWORK_ERROR           = 5;
		const CONFIG_ERROR            = 6;

		/**
		 * Construct firewall error
		 *
		 * @param int $reason
		 * @param string $message
		 */
		function __construct($reason = FirewallError::UNKNOWN_ERROR, $message = null) {
			if(empty($message)) {
				switch($reason) {
					case self::USER_FAILED_LOGIN:
						$message = lang('This account is blocked by firewall due too many failed logins.');
						break;
					case self::TOO_MANY_ATTEMPTS:
						$message = lang('This ip address is blocked by firewall due too many failed logins.');
						break;
					case self::FIXED_RULE:
						$message = lang('This ip address is blocked by firewall.');
						break;
					case self::INVALID_ADDRESS_FORMAT:
						$message = lang('This ip address is not valid.');
						break;
					case self::NETWORK_ERROR:
						$message = lang('Firewall cannot be initialized because IP address cannot be recognized as valid IPv4 nor IPv6.');
						break;
					case self::CONFIG_ERROR:
						$message = lang('Firewall config error.');
						break;
					default:
						$message = lang('Unknown error. Please contact support for assistance');
				} // if
			} // if

			parent::__construct($message, array(
				'reason' => $reason,
			));
		} // __construct
	}