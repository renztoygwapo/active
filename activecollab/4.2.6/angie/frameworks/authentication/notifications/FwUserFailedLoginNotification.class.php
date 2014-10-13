<?php

	/**
	 * User failed login issued notification
	 *
	 * @package activeCollab.modules.authentication
	 * @subpackage notifications
	 */
	abstract class FwUserFailedLoginNotification extends Notification {

		/**
		 * Return notification message in plain text
		 *
		 * @param IUser $user
		 * @return string
		 */
		function getMessage(IUser $user) {
			return lang("More than :max_attempts failed login detected with your account", array(
				'max_attempts'  => $this->getMaxAttempts(),
			));
		} // getMessage

		/**
		 * Return max attempts
		 *
		 * @return string
		 */
		function getMaxAttempts() {
			return $this->getAdditionalProperty('max_attempts');
		} // getMaxAttemptes

		/**
		 * Set max attempts
		 *
		 * @param string $value
		 * @return FwFailedLoginNotification
		 */
		function &setMaxAttempts($value) {
			$this->setAdditionalProperty('max_attempts', $value);

			return $this;
		} // setMaxAttemptes

		/**
		 * Return additional template variables
		 *
		 * @param NotificationChannel $channel
		 * @return array
		 */
		function getAdditionalTemplateVars(NotificationChannel $channel) {
			return array(
				'max_attempts'  => $this->getMaxAttempts()
			);
		} // getAdditionalTemplateVars

	}