<?php

  /**
   * Notify about new conflicts notification
   *
   * @package angie.frameworks.email
   * @subpackage notifications
   */
  class ConflictNotifyOnDailyNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang('New conflicts created', null, true, $user->getLanguage());
    } // getMessage

    /**
     * Return conflicts number
     *
     * @return string
     */
    function getConflictsNum() {
      return $this->getAdditionalProperty('conflict_num');
    } // getConflictsNum

    /**
     * Set conflicts number
     *
     * @param string $value
     * @return ConflictNotifyOnDailyNotification
     */
    function &setConflictsNum($value) {
      $this->setAdditionalProperty('conflict_num', $value);

      return $this;
    } // setConflictsNum

    /**
     * Return conflicts page URL
     *
     * @return string
     */
    function getConflictPageUrl() {
      return $this->getAdditionalProperty('conflict_page_url');
    } // getConflictPageUrl

    /**
     * Set conflicts page URL
     *
     * @param string $value
     * @return ConflictNotifyOnDailyNotification
     */
    function &setConflictPageUrl($value) {
      $this->setAdditionalProperty('conflict_page_url', $value);

      return $this;
    } // setConflictPageUrl

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'conflict_num' => $this->getConflictsNum(),
        'conflict_page_url' => $this->getConflictPageUrl(),
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