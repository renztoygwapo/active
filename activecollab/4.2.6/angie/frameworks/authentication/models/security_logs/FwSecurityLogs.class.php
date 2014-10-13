<?php

/**
 * FwSecurityLogs class
 *
 * @package ActiveCollab.modules.authentication
 * @subpackage models
 */
class FwSecurityLogs extends BaseSecurityLogs {

	/**
	 * Write security log
	 *
	 * @param IUser $user
	 * @param string $event
	 * @param null $someone
	 */
	static function write(IUser $user, $event, $someone = null, $is_api=false) {
		switch ($event) {

			// login
			case 'login':
				// as someone else
				if ($someone instanceof User) {
					DB::execute('INSERT INTO ' . TABLE_PREFIX . 'security_logs (user_id, user_name, user_email, user_agent, login_as_id, login_as_name, login_as_email, event, event_on, user_ip, is_api) VALUES (?, ?, ?, ?, ?, ?, ?, ?, UTC_TIMESTAMP(), ?, ?)', $user->getId(), $user->getDisplayName(), $user->getEmail(), AngieApplication::getVisitorUserAgent(), $someone->getId(), $someone->getName(), $someone->getEmail(), 'login', AngieApplication::getVisitorIp(), $is_api);
				} else {
					// as me
					DB::execute('INSERT INTO ' . TABLE_PREFIX . 'security_logs (user_id, user_name, user_email, user_agent, event, event_on, user_ip, is_api) VALUES (?, ?, ?, ?, ?, UTC_TIMESTAMP(), ?, ?)', $user->getId(), $user->getDisplayName(), $user->getEmail(), AngieApplication::getVisitorUserAgent(), 'login', AngieApplication::getVisitorIp(), $is_api);
				} // if
				break;

			// logout
			case 'logout':
				// someone logout me
				if ($someone instanceof User) {
					DB::execute('INSERT INTO ' . TABLE_PREFIX . 'security_logs (user_id, user_name, user_email, logout_by_id, logout_by_name, logout_by_email, user_agent, event, event_on, user_ip) VALUES (?, ?, ?, ?, ?, ?, ?, ?, UTC_TIMESTAMP(), ?)', $user->getId(), $user->getName(), $user->getEmail(), $someone->getId(), $someone->getName(), $someone->getEmail(), AngieApplication::getVisitorUserAgent(), 'logout', AngieApplication::getVisitorIp());
					// logout by myself
				} else {
					DB::execute('INSERT INTO ' . TABLE_PREFIX . 'security_logs (user_id, user_name, user_email, user_agent, event, event_on, user_ip) VALUES (?, ?, ?, ?, ?, UTC_TIMESTAMP(), ?)', $user->getId(), $user->getName(), $user->getEmail(), AngieApplication::getVisitorUserAgent(), 'logout', AngieApplication::getVisitorIp());
				}
				break;

			// failed
			case 'failed':
				// as someone else
				if ($someone instanceof User) {
					DB::execute('INSERT INTO ' . TABLE_PREFIX . 'security_logs (user_id, user_name, user_email, user_agent, login_as_id, login_as_name, login_as_email, event, event_on, user_ip, is_api) VALUES (?, ?, ?, ?, ?, ?, ?, ?, UTC_TIMESTAMP(), ?, ?)', $user->getId(), $user->getDisplayName(), $user->getEmail(), AngieApplication::getVisitorUserAgent(), $someone->getId(), $someone->getName(), $someone->getEmail(), 'failed', AngieApplication::getVisitorIp(), $is_api);
				} else {
					// as me
					DB::execute('INSERT INTO ' . TABLE_PREFIX . 'security_logs (user_id, user_name, user_email, user_agent, event, event_on, user_ip, is_api) VALUES (?, ?, ?, ?, ?, UTC_TIMESTAMP(), ?, ?)', $user->getId(), $user->getDisplayName(), $user->getEmail(), AngieApplication::getVisitorUserAgent(), 'failed', AngieApplication::getVisitorIp(), $is_api);
				} // if

				self::triggerFirewall($user);
				break;

			default:
				DB::execute('INSERT INTO ' . TABLE_PREFIX . 'security_logs (user_id, user_name, user_email, user_agent, event, event_on, user_ip, is_api) VALUES (?, ?, ?, ?, ?, UTC_TIMESTAMP(), ?, ?)', $user->getId(), $user->getDisplayName(), $user->getEmail(), AngieApplication::getVisitorUserAgent(), 'expired', AngieApplication::getVisitorIp(), $is_api);
				break;
		}
	} // log

	/**
	 * Log security for login try
	 *
	 * @param string $email
	 */
	static function logAttempt($email = null) {
		DB::execute('INSERT INTO ' . TABLE_PREFIX . 'security_logs (user_email, user_agent, event, event_on, user_ip) VALUES (?, ?, ?, UTC_TIMESTAMP(), ?)', $email, AngieApplication::getVisitorUserAgent(), 'failed', AngieApplication::getVisitorIp());
		self::triggerFirewall();
	} // logAttempt

	/**
	 * Trigger Firewall
	 *
	 * @param null $user
	 */
	protected static function triggerFirewall($user=null) {
		$from_ip = AngieApplication::getVisitorIp();
		$total_attempts = DB::executeFirstCell("SELECT COUNT(*) FROM " . TABLE_PREFIX . "security_logs WHERE event = ? AND DATE(event_on) = ? AND user_ip = ?", 'failed', DateValue::now()->toMySQL(), $from_ip);

		if ($total_attempts && AngieApplication::firewall()->isEnabled()) {
			$total_user_attempts = 0;
			if ($user instanceof User) {
				$total_user_attempts = DB::executeFirstCell("SELECT COUNT(*) FROM " . TABLE_PREFIX . "security_logs WHERE event = ? AND DATE(event_on) = ? AND user_ip = ? AND user_id = ?", 'failed', DateValue::now()->toMySQL(), $from_ip, $user->getId());
			} // if

			$max_attempts = AngieApplication::firewall()->getMaxAttempts();
			$alert_user_on = AngieApplication::firewall()->getAlertUserOn();
			$alert_admin_on = AngieApplication::firewall()->getAlertAdminOn();

			if (0 == $total_attempts % $max_attempts) {
				AngieApplication::firewall()->addTempRule($from_ip, $user)->saveConfig();
			} // if

			// notify user if has more than X failed logins on day
			if ($total_user_attempts && ($total_user_attempts == $alert_user_on) && $user instanceof User) {
				AngieApplication::notifications()
					->notifyAbout('user_failed_login')
					->setMaxAttempts(AngieApplication::firewall()->getAlertUserOn())
					->sendToUsers($user);
			} // if

			// notify admins if has more than X failed logins on day
			if ($total_attempts == $alert_admin_on) {
				AngieApplication::notifications()
					->notifyAbout('failed_login')
					->setMaxAttempts(AngieApplication::firewall()->getAlertAdminOn())
					->setFromIP($from_ip)
					->sendToAdministrators(true);
			} // if
		} // if
	} // triggerFirewall

	/**
	 * Log failed api token
	 */
	static function countFailedToken() {
		$today = new DateValue();

		$log_id = DB::executeFirstCell("SELECT id FROM " . TABLE_PREFIX . "api_token_logs WHERE counts_on = ?", $today->toMySQL());

		// update
		if ($log_id) {
			DB::execute('UPDATE ' . TABLE_PREFIX . 'api_token_logs SET total = total + 1 WHERE id = ?', $log_id);
		// insert
		} else {
			DB::execute('INSERT INTO ' . TABLE_PREFIX . 'api_token_logs (counts_on, total) VALUES (?, ?)', $today->toMySQL(), 1);
		} // if
	} // countFailedToken

}