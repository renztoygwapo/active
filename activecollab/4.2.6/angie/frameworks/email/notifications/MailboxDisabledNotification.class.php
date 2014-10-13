<?php

  /**
   * Notify when mailbox is disabled notification
   *
   * @package angie.frameworks.email
   * @subpackage notifications
   */
  class MailboxDisabledNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang('Mailbox ":name" has been Disabled', array(
        'name' => $this->getMailboxName()
      ), true, $user->getLanguage());
    } // getMessage

    /**
     * Return resolve url
     *
     * @return string
     */
    function getResolveUrl() {
      return $this->getAdditionalProperty('resolve_url');
    } // getResolveUrl

    /**
     * Set resolve url
     *
     * @param string $value
     * @return MailboxDisabledNotification
     */
    function &setResolveUrl($value) {
      $this->setAdditionalProperty('resolve_url', $value);

      return $this;
    } // setResolveUrl

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
     * @return MailboxDisabledNotification
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
        'resolve_url' => $this->getResolveUrl(),
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