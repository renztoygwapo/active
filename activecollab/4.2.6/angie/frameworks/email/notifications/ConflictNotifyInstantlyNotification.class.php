<?php

  /**
   * Conflict created notification
   *
   * @package angie.frameworks.email
   * @subpackage notifications
   */
  class ConflictNotifyInstantlyNotification extends Notification {

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
     * Return parent pending mail
     *
     * @return RecurringProfile
     */
    function getPendingMail() {
      return DataObjectPool::get('IncomingMail', $this->getAdditionalProperty('incoming_mail_id'));
    } // getPendingMail

    /**
     * Set incoming mail
     *
     * @param IncomingMail $pending_mail
     * @return ConflictNotifyInstantlyNotification
     */
    function &setPendingMail(IncomingMail $pending_mail) {
      $this->setAdditionalProperty('incoming_mail_id', $pending_mail->getId());

      return $this;
    } // setPendingMail

    /**
     * Return pending mail
     *
     * @return string
     */
    function getConflictsNum() {
      return $this->getAdditionalProperty('pending_email');
    } // getConflictsNum

    /**
     * Set conflicts number
     *
     * @param string $value
     * @return ConflictNotifyInstantlyNotification
     */
    function &setConflictsNum($value) {
      $this->setAdditionalProperty('pending_email', $value);

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
     * @return ConflictNotifyInstantlyNotification
     */
    function &setConflictPageUrl($value) {
      $this->setAdditionalProperty('conflict_page_url', $value);

      return $this;
    } // setConflictPageUrl

    /**
     * Return conflict reason
     *
     * @return string
     */
    function getConflictReason() {
      return $this->getAdditionalProperty('conflict_reason');
    } // getConflictReason

    /**
     * Set conflicts page URL
     *
     * @param string $value
     * @return ConflictNotifyInstantlyNotification
     */
    function &setConflictReason($value) {
      $this->setAdditionalProperty('conflict_reason', $value);

      return $this;
    } // setConflictReason

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'conflict_reason' => $this->getConflictReason(),
        'conflict_page_url' => $this->getConflictPageUrl(),
        'pending_email' => $this->getPendingMail()
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