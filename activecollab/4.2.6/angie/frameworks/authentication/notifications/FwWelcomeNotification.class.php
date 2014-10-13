<?php

  /**
   * Welcome notification
   *
   * @package angie.frameworks.authentication
   * @subpackage notifications
   */
  abstract class FwWelcomeNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("An account has been created for You", null, true, $user->getLanguage());
    } // getMessage

    /**
     * Cached password value
     *
     * @var string
     */
    private $password;

    /**
     * Return password value
     *
     * @return string
     */
    function getPassword() {
      return $this->password;
    } // getPassword

    /**
     * Set password value (temporaly only)
     *
     * @param $value
     * @return WelcomeNotification
     */
    function &setPassword($value) {
      $this->password = $value;

      return $this;
    } // setPassword

    /**
     * Return welcome message
     *
     * @return string
     */
    function getWelcomeMessage() {
      return $this->getAdditionalProperty('welcome_message');
    } // getWelcomeMessage

    /**
     * Set welcome message
     *
     * @param $value
     * @return WelcomeNotification
     */
    function &setWelcomeMessage($value) {
      $this->setAdditionalProperty('welcome_message', $value);

      return $this;
    } // setWelcomeMessage

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'password' => $this->getPassword(),
        'welcome_message' => $this->getWelcomeMessage(),
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
        return true; // Always deliver this notification via email
      } elseif($channel instanceof WebInterfaceNotificationChannel) {
        return false; // Never deliver this notification to web interface
      } // if

      return parent::isThisNotificationVisibleInChannel($channel, $recipient);
    } // isThisNotificationVisibleInChannel

  }