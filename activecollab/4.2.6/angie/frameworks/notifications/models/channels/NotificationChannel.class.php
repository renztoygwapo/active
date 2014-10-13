<?php

  /**
   * Base notification channel
   *
   * @package angie.frameworks.notifications
   * @subpackage models
   */
  abstract class NotificationChannel {

    /**
     * Cached short name
     *
     * @var bool
     */
    private $short_name = false;

    /**
     * Return short name
     *
     * @return string
     */
    function getShortName() {
      if($this->short_name === false) {
        $class_name = get_class($this);

        $this->short_name = Inflector::underscore(substr($class_name, 0, strlen($class_name) - 19));
      } // if

      return $this->short_name;
    } // getShortName

    /**
     * Return verbose name of the channel
     *
     * @return string
     */
    abstract function getVerboseName();
    
    // ---------------------------------------------------
    //  Enable / Disable / Settings
    // ---------------------------------------------------

    /**
     * Returns true if this channel is enabled by default
     *
     * @return boolean
     */
    function isEnabledByDefault() {
      return ConfigOptions::getValue($this->getShortName() . '_notifications_enabled');
    } // isEnabledByDefault

    /**
     * Set enabled by default
     *
     * @param boolean $value
     */
    function setEnabledByDefault($value) {
      ConfigOptions::setValue($this->getShortName() . '_notifications_enabled', (boolean) $value);
    } // setEnabledByDefault

    /**
     * Returns true if this channel is enabled for this user
     *
     * @param User $user
     * @return boolean
     */
    function isEnabledFor(User $user) {
      if($this->canOverrideDefaultStatus($user)) {
        return ConfigOptions::getValueFor($this->getShortName() . '_notifications_enabled', $user);
      } else {
        return $this->isEnabledByDefault(); // Use default value
      } // if
    } // isEnabledFor

    /**
     * Set enabled for given user
     *
     * @param User $user
     * @param boolean|null $value
     * @throws InvalidParamError
     */
    function setEnabledFor(User $user, $value) {
      if($value === true || $value === false) {
        ConfigOptions::setValueFor($this->getShortName() . '_notifications_enabled', $user, $value);
      } elseif($value === null) {
        ConfigOptions::removeValuesFor($user, $this->getShortName() . '_notifications_enabled');
      } else {
        throw new InvalidParamError('value', $value, '$value can be BOOL value or NULL');
      } // if
    } // setEnabledFor

    /**
     * Returns true if $user can override default enable / disable status
     *
     * @param User $user
     * @return boolean
     */
    function canOverrideDefaultStatus(User $user) {
      if($user->isAdministrator()) {
        return true;
      } else {
        $who_can_override = $this->whoCanOverrideDefaultStatus();

        return is_array($who_can_override) && in_array(get_class($user), $who_can_override);
      } // if
    } // canOverrideDefaultStatus

    /**
     * Return a list of roles that can
     *
     * @return array
     */
    function whoCanOverrideDefaultStatus() {
      $settings = ConfigOptions::getValue('who_can_override_channel_settings');

      return is_array($settings) && isset($settings[$this->getShortName()]) && is_foreachable($settings[$this->getShortName()]) ? $settings[$this->getShortName()] : null;
    } // whoCanOverrideDefaultStatus

    /**
     * Set who can override default settings for this channel
     *
     * @param $roles
     */
    function setWhoCanOverrideDefaultStatus($roles) {
      $settings = ConfigOptions::getValue('who_can_override_channel_settings');

      if(!is_array($settings)) {
        $settings = array();
      } // if

      $settings[$this->getShortName()] = $roles;

      ConfigOptions::setValue('who_can_override_channel_settings', $settings);
    } // setWhoCanOverrideDefaultStatus
    
    // ---------------------------------------------------
    //  Open / Close
    // ---------------------------------------------------

    /**
     * Open channel for sending
     */
    function open() {
      Logger::log('Channel ' . get_class($this) . ' has been openned', Logger::INFO);
    } // open

    /**
     * Close channel after notifications have been sent
     */
    function close($sending_interupted = false) {
      if($sending_interupted) {
        Logger::log('Sending has been interupted. Channel ' . get_class($this) . ' has been closed', Logger::INFO);
      } else {
        Logger::log('Sending done. Channel ' . get_class($this) . ' has been closed', Logger::INFO);
      } // if
    } // close

    /**
     * Send notification via this channel
     *
     * @param Notification $notification
     * @param IUser $recipient
     * @param boolean $skip_sending_queue
     */
    abstract function send(Notification &$notification, IUser $recipient, $skip_sending_queue = false);

  }