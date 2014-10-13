<?php

  /**
   * Notify when mailbox is not checked notification
   *
   * @package angie.frameworks.email
   * @subpackage notifications
   */
  class MailboxNotCheckedNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang('Mailbox ":name" has not been Checked', array(
        'name' => $this->getMailboxName()
      ), true, $user->getLanguage());
    } // getMessage

    /**
     * Return error
     *
     * @return string
     */
    function getError() {
      return $this->getAdditionalProperty('error');
    } // getResolveUrl

    /**
     * Set Error
     *
     * @param string $value
     * @return MailboxNotCheckedNotification
     */
    function &setError($value) {
      $this->setAdditionalProperty('error', $value);

      return $this;
    } // setError

    /**
     * Return conflicts page URL
     *
     * @return string
     */
    function getMailboxName() {
      return $this->getAdditionalProperty('mailbox_name');
    } // getMailboxName

    /**
     * Set conflicts page URL
     *
     * @param string $value
     * @return MailboxNotCheckedNotification
     */
    function &setMailboxName($value) {
      $this->setAdditionalProperty('mailbox_name', $value);

      return $this;
    } // setMailboxName

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'error' => $this->getError(),
        'mailbox_name' => $this->getMailboxName(),
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