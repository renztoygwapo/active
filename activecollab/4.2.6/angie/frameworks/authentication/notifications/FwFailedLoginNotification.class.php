<?php

  /**
   * Failed login issued notification
   *
   * @package activeCollab.modules.authentication
   * @subpackage notifications
   */
  abstract class FwFailedLoginNotification extends Notification {

    /**
     * Return notification message in plain text
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("There are more than :max_attempts failed login from :from_ip.", array(
        'max_attempts'  => $this->getMaxAttempts(),
        'from_ip'       => $this->getFromIP()
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
	   * Return from IP address
	   *
	   * @return string
	   */
	  function getFromIP() {
		  return $this->getAdditionalProperty('from_ip');
	  } // getMaxAttemptes

	  /**
	   * Set from IP address
	   *
	   * @param string $value
	   * @return FwFailedLoginNotification
	   */
	  function &setFromIP($value) {
		  $this->setAdditionalProperty('from_ip', $value);

		  return $this;
	  } // setFromIP

	  /**
	   * Return additional template variables
	   *
	   * @param NotificationChannel $channel
	   * @return array
	   */
	  function getAdditionalTemplateVars(NotificationChannel $channel) {
		  return array(
			  'max_attempts'  => $this->getMaxAttempts(),
			  'from_ip'       => $this->getFromIP()
		  );
	  } // getAdditionalTemplateVars

    /**
     * This notification should not be displayed in web interface
     *
     * @param NotificationChannel $channel
     * @param IUser $recipient
     * @return bool
     */
    function isThisNotificationVisibleInChannel(NotificationChannel $channel, IUser $recipient) {
      if($channel instanceof EmailNotificationChannel) {
        return true; // Force email, regardless of settings
      } // if

      return parent::isThisNotificationVisibleInChannel($channel, $recipient);
    } // isThisNotificationVisibleInChannel

  }