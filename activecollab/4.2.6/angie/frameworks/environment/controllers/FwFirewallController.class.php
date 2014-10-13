<?php

	// Extend administration controller
	AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

	/**
	 * Firewall controller
	 *
	 * @package angie.frameworks.environment
	 * @subpackage controllers
	 */
	abstract class FwFirewallController extends AdminController {

		/**
		 * Prepare controller
		 */
		function __before() {
			parent::__before();
		} // __construct

		/**
		 * Display firewall configuration
		 */
		function index() {
			if($this->request->isAsyncCall()) {
				$firewall_data = $this->request->post('firewall');
				if(!is_array($firewall_data)) {
					$white_list = AngieApplication::firewall()->getWhiteList();
					$black_list = AngieApplication::firewall()->getBlackList();
					$firewall_data = array(
						'enabled'               => AngieApplication::firewall()->isEnabled(),
						'white_list_enabled'    => is_foreachable($white_list) ? 1 : 0,
						'white_list'            => implode(PHP_EOL, $white_list),
						'black_list_enabled'    => is_foreachable($black_list) ? 1 : 0,
						'black_list'            => implode(PHP_EOL, $black_list),
						'custom_settings'       => AngieApplication::firewall()->isCustomized(),
						'max_attempts'          => AngieApplication::firewall()->getMaxAttempts(),
						'block_time'            => AngieApplication::firewall()->getMinBlockTime(), //minutes
						'alert_user_on'         => AngieApplication::firewall()->getAlertUserOn(),
						'alert_admin_on'        => AngieApplication::firewall()->getAlertAdminOn(),
					);
				} // if

				$temp_rules = AngieApplication::firewall()->removeExpiredRules()->saveConfig()->getTempList();
				if (is_foreachable($temp_rules)) {
					foreach ($temp_rules as &$rule) {
						$id = array_var($rule, 'id', 0);
						$network = array_var($rule, 'network');
						$email = array_var($rule, 'email');
						if ($email && is_valid_email($email)) {
							$blocked_user = Users::findByEmail($email);
						} else {
							$email = lang('everyone');
						}// if
						$time = array_var($rule, 'time');
						$rule = array(
							'id'    => $id,
							'text'  => lang('Deny <b>:name</b> from <b>:network</b> until <b>:time</b>', array(
								'name'        => $blocked_user instanceof User ? $blocked_user->getName() : $email,
								'network'     => $network,
								'time'        => DateTimeValue::makeFromTimestamp($time)->formatForUser()
							))
						);
					} // foreach
				} // if

				$this->response->assign(array(
					'firewall_data' => $firewall_data,
					'temp_rules'    => $temp_rules
				));

				if($this->request->isSubmitted()) {

					$enabled = (boolean) array_var($firewall_data, 'enabled');

					$white_list_enabled = (boolean) array_var($firewall_data, 'white_list_enabled');
					if ($white_list_enabled) {
						$white_list = explode(PHP_EOL, array_var($firewall_data, 'white_list'));
					} else {
						$white_list = array();
					} // if

					$black_list_enabled = (boolean) array_var($firewall_data, 'black_list_enabled');
					if ($black_list_enabled) {
						$black_list = explode(PHP_EOL, array_var($firewall_data, 'black_list'));
					} else {
						$black_list = array();
					} // if

					$temp_rule_ids = array_var($firewall_data, 'temp_rule_ids', array());

					try {
						$custom_settings = (boolean) array_var($firewall_data, 'custom_settings');
						if ($custom_settings) {
							AngieApplication::firewall()
								->setMaxAttempts(array_var($firewall_data, 'max_attempts'))
								->setMinBlockTime(array_var($firewall_data, 'block_time'))
								->setAlertUserOn(array_var($firewall_data, 'alert_user_on'))
								->setAlertAdminOn(array_var($firewall_data, 'alert_admin_on'));
						} else {
							AngieApplication::firewall()->setDefaultSettings();
						} // if

						AngieApplication::firewall()
							->setEnabled($enabled)
							->removeTempRule($temp_rule_ids)
							->setWhiteList($white_list)
							->setBlackList($black_list)
							->saveConfig();

						// kill all sessions which IP's is now on black list
						$map_session_id_ip = Authentication::getProvider()->mapSessionIDtoIP();
						$sessions_to_kill = array();
						if (is_foreachable($map_session_id_ip)) {
							foreach ($map_session_id_ip as $key => $value) {
								if (!AngieApplication::firewall()->check($value)) {
									$sessions_to_kill[] = $key;
								} // if
							} // foreach
						} // if
						if ($sessions_to_kill) {
							Authentication::getProvider()->killSessions($sessions_to_kill);
						} // if
					} catch (Exception $e) {
						$this->response->exception($e);
					} // try

					$this->response->ok();
				} // if
			} else {
				$this->response->badRequest();
			} // if
		} // index

	}