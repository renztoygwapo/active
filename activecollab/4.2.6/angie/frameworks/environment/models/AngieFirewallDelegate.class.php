<?php

	/**
  * Angie firewall delegate implementation
  *
  * @package angie.library.application
  * @subpackage delegates
	*/
	class AngieFirewallDelegate extends AngieDelegate {

		// config options
		const FIREWALL_WHITE_LIST   = 'firewall_white_list';
		const FIREWALL_BLACK_LIST   = 'firewall_black_list';
		const FIREWALL_TEMP_LIST    = 'firewall_temp_list';
		const FIREWALL_ENABLED      = 'firewall_enabled';
		const FIREWALL_SETTINGS     = 'firewall_settings';

		protected $white_list       = null;
		protected $black_list       = null;
		protected $rules_to_add     = array();
		protected $rules_to_remove  = array();
		protected $settings         = null;
		protected $enabled          = null;

		private   $ip               = null;
		private   $network_filter   = null;

		/**
		 * Initialize Firewall: Network Filter, White list, Black list
		 */
		function initialize() {
			$this->ip = AngieApplication::getVisitorIp();

			$this->network_filter = $this->getNetworkFilter();

			// Throw Exception if there is something wrong with IP address
			if (!$this->network_filter) {
				die(lang('Warning: system cannot create filter for address ":address". You need to login from valid IPv4 or IPv6 address.', array('address' => $this->ip)));
			} // if

			// If firewall is enabled check visitor IP address
			if ($this->isEnabled()) {
				if ((!$this->isOnWhiteList($this->ip) && $this->isOnBlackList($this->ip)) || ($this->isOnWhiteList($this->ip) && $this->isOnBlackList($this->ip))) {
					header('HTTP/1.1 403 Forbidden');
					die('<h1>HTTP/1.1 403 Forbidden by Firewall</h1>');
				} // if
			} // if
		} // initialize

		//---------------------------------------
		// White List
		//---------------------------------------

		/**
		 * Get allowed addresses
		 *
		 * @return array
		 */
		function getWhiteList() {
			return ConfigOptions::getValue(self::FIREWALL_WHITE_LIST);
		} // getWhiteList

		/**
		 * Set white list
		 *
		 * @param null $list
		 * @return $this
		 */
		function setWhiteList($list=null) {
			if ($list !== null) {
				if (!is_array($list)) {
					$list = array($list);
				} // if

				if (is_foreachable($list)) {
					foreach ($list as $key => &$value) {
						$value = trim($value);
						if (empty($value)) {
							unset($list[$key]);
							continue;
						} // if
					} // foreach
				} // if

				$this->white_list = $list;
			} else {
				$this->white_list = array();
			} // if

			return $this;
		} // setWhiteList

		//---------------------------------------
		// Black List
		//---------------------------------------

		/**
		 * Get blocked addresses
		 *
		 * @return array
		 */
		function getBlackList() {
			return ConfigOptions::getValue(self::FIREWALL_BLACK_LIST);
		} // getBlacklist

		/**
		 * Set black list
		 *
		 * @param null $list
		 * @return $this
		 */
		function setBlackList($list=null) {
			if ($list !== null) {
				if (!is_array($list)) {
					$list = array($list);
				} // if

				if (is_foreachable($list)) {
					foreach ($list as $key => &$value) {
						$value = trim($value);
						if (empty($value)) {
							unset($list[$key]);
							continue;
						} // if
					} // foreach
				} // if

				$this->black_list = $list;
			} else {
				$this->black_list = array();
			} // if

			return $this;
		} // setBlackList

		//---------------------------------------
		// Temp List
		//---------------------------------------

		/**
		 * Get temp list
		 *
		 * @return array
		 */
		function getTempList() {
			return ConfigOptions::getValue(self::FIREWALL_TEMP_LIST);
		} // getTempList

		/**
		 * Add temp rule
		 *
		 * @param $ip_address
		 * @param null $user
		 * @return $this
		 */
		function addTempRule($ip_address, $user=null) {
			if ($this->validate($ip_address)) {
				$min_block_time = $this->getMinBlockTime() * 60;
				$this->rules_to_add[] = array(
					'network'  => trim($ip_address),
					'email'    => $user instanceof User ? $user->getEmail() : "",
					'time'     => DateTimeValue::now()->advance($min_block_time, false)->getTimestamp()
				);
			} // if

			return $this;
		} // extendTempList

		/**
		 * Remove temp rule
		 *
		 * @param $id
		 * @return $this
		 */
		function removeTempRule($id) {
			if (!is_array($id)) {
				$id = array($id);
			} // if

			if (is_foreachable($id)) {
				foreach ($id as $key => $value) {
					$value = (integer) $value;
					if (empty($value)) {
						unset($id[$key]);
						continue;
					} // if

					$this->rules_to_remove[] = $value;
				} // foreach
			} // if

			return $this;
		} // removeTempRule

		/**
		 * Remove all expired rules
		 *
		 * @return $this
		 */
		function removeExpiredRules() {
			$now = new DateTimeValue();
			$rules = $this->getTempList();
			if (is_foreachable($rules)) {
				foreach ($rules as $rule) {
					$time = DateTimeValue::makeFromTimestamp(array_var($rule, 'time', 0));
					if ($time->getTimestamp() < $now->getTimestamp()) {
						$this->removeTempRule(array_var($rule, 'id', 0));
					} // if
				} // foreach
			} // if

			return $this;
		} // removeExpiredRules

		//---------------------------------------
		// Settings
		//---------------------------------------

		/**
		 * Check if firewall is enabled
		 *
		 * @return bool
		 */
		function isEnabled() {
			return ConfigOptions::getValue(self::FIREWALL_ENABLED);
		} // isEnabled

		/**
		 * Set enabled or disabled
		 *
		 * @param bool $default
		 * @return $this
		 */
		function setEnabled($default=true) {
			$this->enabled = (boolean) $default;
			return $this;
		} // setEnabled

		/**
		 * Send user mail after n failed login
		 *
		 * @param $int
		 * @return $this
		 */
		function setAlertUserOn($int) {
			return $this->setSettings('alert_user_on', (integer) $int);
		} // setAlertUserOn

		/**
		 * Get alert user on
		 *
		 * @return int
		 */
		function getAlertUserOn() {
			return $this->getSettings('alert_user_on', 5);
		} // getAlertUserOn

		/**
		 * Send admin mail after n failed login
		 *
		 * @param $int
		 * @return $this
		 */
		function setAlertAdminOn($int) {
			return $this->setSettings('alert_admin_on', (integer) $int);
		} // setAlertAdminOn

		/**
		 * Get alert admin on
		 *
		 * @return int
		 */
		function getAlertAdminOn() {
			return $this->getSettings('alert_admin_on', 10);
		} // getAlertAdminOn

		/**
		 * Set max allowed attempts
		 *
		 * @param $int
		 * @return $this
		 */
		function setMaxAttempts($int) {
			return $this->setSettings('max_attempts', (integer) $int);
		} // setMaxAttempts

		/**
		 * Get max allowed attempts
		 *
		 * @return int
		 */
		function getMaxAttempts() {
			return $this->getSettings('max_attempts', 5);
		} // getMaxAttempts

		/**
		 * Set min block time in seconds
		 *
		 * @param $int
		 * @return $this
		 */
		function setMinBlockTime($int) {
			return $this->setSettings('min_block_time', (integer) $int);
		} // setMinBlockTime

		/**
		 * Get min block time in seconds
		 */
		function getMinBlockTime() {
			return $this->getSettings('min_block_time', 5);
		} // getMinBlockTime

		/**
		 * Check is default settings customized
		 *
		 * @return bool
		 */
		function isCustomized() {
			$settings = $this->getSettings();
			return is_foreachable($settings) ? true : false;
		} // isCustomized

		/**
		 * Set default settings
		 *
		 * @return $this
		 */
		function setDefaultSettings() {
			$this->settings = array();

			return $this;
		} // setDefaultSettings

		/**
		 * Set settings value based on key
		 *
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		protected function setSettings($key, $value) {
			if ($this->settings === null || !is_array($this->settings)) {
				$this->setDefaultSettings();
			} // if

			$this->settings[$key] = $value;

			return $this;
		} // setSettings

		/**
		 * Get all settings or specified by key
		 *
		 * @param null $key
		 * @param null $default
		 * @return mixed
		 */
		function getSettings($key=null, $default=null) {
			$settings = ConfigOptions::getValue(self::FIREWALL_SETTINGS);
			if ($key === null) {
				return $settings;
			} // if
			return array_var($settings, $key, $default);
		} // getSettings

		//---------------------------------------
		// Save Config
		//---------------------------------------

		/**
		 * Save lists and settings
		 *
		 * @return $this
		 * @throws FirewallError
		 */
		function saveConfig() {
			$errors = array();
			// Check white list
			if ($this->white_list !== null) {
				$match = true;
				if (is_foreachable($this->white_list)) {
					$match = false;
					$incorrect_values = array();
					foreach ($this->white_list as $white_list_value) {
						if (!$this->validate($white_list_value)) {
							$incorrect_values[] = $white_list_value;
						} // if
						if ($this->network_filter->__invoke($white_list_value, $this->ip)) {
							$match = true;
						} // if
					} // foreach
					if ($incorrect_values) {
						$errors[] = lang('This ":value" address or range are invalid on allow list', array('value' => implode(', ', $incorrect_values)));
					} // if
				} // if
				if (!$match) {
					$errors[] = lang('IP address ":ip_address" of this session need to be on allow list, please specify it and then save again', array('ip_address' => $this->ip));
				} // if
			} // if

			// Check black list
			if ($this->black_list !== null) {
				$match = false;
				if (is_foreachable($this->black_list)) {
					$incorrect_values = array();
					foreach ($this->black_list as $black_list_value) {
						if (!$this->validate($black_list_value)) {
							$incorrect_values[] = $black_list_value;
						} // if
						if ($this->network_filter->__invoke($black_list_value, $this->ip)) {
							$match = true;
						} // if
					} // foreach
					if ($incorrect_values) {
						$errors[] = lang('This ":value" address or range are invalid on deny list', array('value' => implode(', ', $incorrect_values)));
					} // if
				} // if
				if ($match) {
					$errors[] = lang('This ":value" is your IP address which cannot be blocked right now, please remove it from list and then save again', array('value' => $black_list_value));
				} // if
			} // if

			if ($errors) {
				throw new FirewallError(FirewallError::CONFIG_ERROR, implode(". ", $errors));
			} // if

			// Save white list
			if ($this->white_list !== null) {
				ConfigOptions::setValue(self::FIREWALL_WHITE_LIST, $this->white_list);
				$this->white_list = null;
			} // if

			// Save black list
			if ($this->black_list !== null) {
				ConfigOptions::setValue(self::FIREWALL_BLACK_LIST, $this->black_list);
				$this->black_list = null;
			} // if

			// Temp list
			$temp_list = $this->getTempList();
			$new_temp_list = array();
			$temp_id_pool = array();
			if (is_foreachable($temp_list)) {
				foreach($temp_list as $temp_rule) {
					$temp_id = array_var($temp_rule, 'id');
					if (!in_array($temp_id, $this->rules_to_remove)) {
						$new_temp_list[] = $temp_rule;
						$temp_id_pool[] = $temp_id;
					} // if
				} // foreach
			} // if
			if (is_foreachable($this->rules_to_add)) {
				foreach ($this->rules_to_add as $new_rule) {
					$id = rand(1000, 9999);
					while(in_array($id, $temp_id_pool)) {
						$id = rand(1000, 9999);
					} // while
					$temp_id_pool[] = $id;
					$new_rule['id'] = $id;
					$new_temp_list[] = $new_rule;
				} // foreach
				$this->rules_to_add = array();
			} // if

			ConfigOptions::setValue(self::FIREWALL_TEMP_LIST, $new_temp_list);

			// Enabled/Disabled
			if ($this->enabled !== null) {
				ConfigOptions::setValue(self::FIREWALL_ENABLED, $this->enabled);
				$this->enabled = null;
			} // if

			// Settings
			if ($this->settings !== null) {
				ConfigOptions::setValue(self::FIREWALL_SETTINGS, $this->settings);
				$this->settings = null;
			} // if

			return $this;
		} // saveConfig

		//---------------------------------------
		// Check
		//---------------------------------------

		/**
		 * Check user IP address can pass firewall
		 *
		 * @param string $ip_address
		 * @param User $user
		 * @return bool
		 */
		function check($ip_address, $user=null) {
			if ($this->isEnabled()) {
				if ((!$this->isOnWhiteList($ip_address) && $this->isOnBlackList($ip_address)) || ($this->isOnWhiteList($ip_address) && $this->isOnBlackList($ip_address))) {
					return false;
				} // if

				if ($this->isOnTempList($ip_address, $user)) {
					return false;
				}
			} // if

			return true;
		} // check

		/**
		 * Check White List
		 *
		 * @param string $ip_address
		 * @return bool
		 */
		private function isOnWhiteList($ip_address) {
			$white_list = $this->getWhiteList();

			if (is_foreachable($white_list)) {
				foreach ($white_list as $network) {
					if ($ip_address == $network) {
						return true;
					} else {
						if ($this->network_filter->__invoke($network, $ip_address)) {
							return true;
						} // if
					} // if
				} // foreach

				return false;
			} // if

			return true;
		} // isOnWhiteList

		/**
		 * Check Black List
		 *
		 * @param string $ip_address
		 * @return bool
		 */
		private function isOnBlackList($ip_address) {
			$black_list = $this->getBlackList();

			if (is_foreachable($black_list)) {
				foreach ($black_list as $network) {
					if ($ip_address == $network) {
						return true;
					} else {
						if ($this->network_filter->__invoke($network, $ip_address)) {
							return true;
						} // if
					} // if
				} // foreach
			} // if

			return false;
		} // isOnBlackList

		/**
		 * Check temp list
		 *
		 * @param string $ip_address
		 * @param User $user
		 * @return bool
		 */
		private function isOnTempList($ip_address, $user=null) {
			$this->removeExpiredRules()->saveConfig();

			$temp_list = $this->getTempList();

			if (is_foreachable($temp_list)) {
				foreach ($temp_list as $value) {
					$network = array_var($value, 'network');
					$email = array_var($value, 'email');
					$time = array_var($value, 'time');

					$now = new DateTimeValue();
					$time = DateTimeValue::makeFromTimestamp($time);

					if ($now->getTimestamp() < $time->getTimestamp()) {
						if ($ip_address == $network || $this->network_filter->__invoke($network, $ip_address)) {
							if (!$email) {
								return true;
							} else {
								if ($user instanceof User && $user->getEmail() == $email) {
									return true;
								} // if
							} // if
						} // if
					} // if
				} // foreach
			} // if

			return false;
		} // isOnTempList

		/**
		 * @param $value
		 * @return bool
		 */
		private function validate($value) {
			if (strpos($value, '/')) {
				list($value, $mask) = explode('/', $value);
			} // if

			// IPv4
			if (filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
				if (isset($mask) && ($mask < 1 || $mask > 30)) {
					return false;
				} // if
				return true;
			// IPv6
			} elseif (filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
				if (isset($mask) && ($mask < 1 || $mask > 128)) {
					return false;
				} // if
				return true;
			} // if

			return false;
		} // validate

		/**
		 * Get network filter based on IP version (IPv4, IPv6)
		 *
		 * @return callable
		 * @throws FirewallError
		 */
		private function getNetworkFilter() {
			$ipv4 = filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
			$ipv6 = filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);

			if ($ipv4) {
				$result = function($network, $ip_address) {
					// Wildcard
					if (strpos($network, '*')) {
						$allowed_ip_arr = explode('.', $network);
						$ip_arr = explode('.', $ip_address);
						for ($i = 0; $i < count($allowed_ip_arr); $i++) {
							if ($allowed_ip_arr[$i] == '*') {
								return true;
							} else {
								if (false == ($allowed_ip_arr[$i] == $ip_arr[$i])) {
									return false;
								} // if
							} // if
						} // for
					} // if

					// Mask or CIDR
					if (strpos($network, '/')) {
						$tmp = explode('/', $network);
						if (strpos($tmp[1], '.')) {
							list($allowed_ip_ip, $allowed_ip_mask) = explode('/', $network);
							$begin = (ip2long($allowed_ip_ip) & ip2long($allowed_ip_mask)) + 1;
							$end = (ip2long($allowed_ip_ip) | (~ ip2long($allowed_ip_mask))) + 1;
							$ip = ip2long($ip_address);
							return ($ip >= $begin && $ip <= $end);
						} else {
							list ($net, $mask) = explode('/', $network);
							return ( ip2long($ip_address) & ~((1 << (32 - $mask)) - 1) ) == ip2long($net);
						} // if
					} //if

					// Section
					if (strpos($network, '-')) {
						list($begin, $end) = explode('-', $network);
						$begin = ip2long($begin);
						$end = ip2long($end);
						$ip = ip2long($ip_address);
						return ($ip >= $begin && $ip <= $end);
					} // if

					// Single
					if (ip2long($network)) {
						return (ip2long($network) == ip2long($ip_address));
					} // if

					return false;
				};
			} elseif ($ipv6) {
				$result = function($network, $ip_address) {

					// CIDR
					if (strpos($network, '/')) {

						// Split in address and prefix length
						list($firstaddrstr, $prefixlen) = explode('/', $network);

						// Parse the address into a binary string
						$firstaddrbin = inet_pton($firstaddrstr);

						// Convert the binary string to a string with hexadecimal characters
						# unpack() can be replaced with bin2hex()
						# unpack() is used for symmetry with pack() below
						$firstaddrhex = reset(unpack('H*', $firstaddrbin));

						// Calculate the number of 'flexible' bits
						$flexbits = 128 - $prefixlen;

						// Build the hexadecimal string of the last address
						$lastaddrhex = $firstaddrhex;

						// We start at the end of the string (which is always 32 characters long)
						$pos = 31;
						while ($flexbits > 0) {
							// Get the character at this position
							$orig = substr($lastaddrhex, $pos, 1);

							// Convert it to an integer
							$origval = hexdec($orig);

							// OR it with (2^flexbits)-1, with flexbits limited to 4 at a time
							$newval = $origval | (pow(2, min(4, $flexbits)) - 1);

							// Convert it back to a hexadecimal character
							$new = dechex($newval);

							// And put that character back in the string
							$lastaddrhex = substr_replace($lastaddrhex, $new, $pos, 1);

							// We processed one nibble, move to previous position
							$flexbits -= 4;
							$pos -= 1;
						}

						// Convert the hexadecimal string to a binary string
						# Using pack() here
						# Newer PHP version can use hex2bin()
						$lastaddrbin = pack('H*', $lastaddrhex);

						$ip_address = inet_pton($ip_address);

						return ((strlen($firstaddrbin) == strlen($lastaddrbin)) && ($ip_address >= $firstaddrbin && $ip_address <= $lastaddrbin));
					} // if

					// Single
					if (filter_var($network, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
						return (inet_pton($ip_address) == inet_pton($network));
					} // if

					return false;
				};
			} else {
				return false;
			} // if

			return $result;
		} // getNetworkFilter

	}