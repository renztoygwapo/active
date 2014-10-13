<?php

  /**
   * Invoice reminder notification
   *
   * @package activeCollab.modules.invoicing
   * @subpackage notifications
   */
  class InvoiceReminderNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      $parent = $this->getParent();

      if($parent instanceof Invoice) {
        if($parent->isOverdue()) {
          return lang("Invoice ':name' is Overdue", array(
            'name' => $parent->getName(),
          ), true, $user->getLanguage());
        } else {
          return lang("Invoice ':name' Reminder", array(
            'name' => $parent->getName(),
          ), true, $user->getLanguage());
        } // if
      } else {
        return lang('Unknown', null, true, $user->getLanguage());
      } // if
    } // getMessage

    /**
     * Return files attached to this notification, if any
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAttachments(NotificationChannel $channel) {
      return $this->getParent() instanceof Invoice ? array(
        $this->getParent()->getPdfAttachmentPath() => 'invoice.pdf',
      ) : null;
    } // getAttachments

    /**
     * Get reminder message
     *
     * @return string
     */
    function getReminderMessage() {
      return $this->getAdditionalProperty('reminder_message');
    } // getMessage

    /**
     * Set reminder message
     *
     * @param string $value
     * @return InvoiceReminderNotification
     */
    function &setReminderMessage($value) {
      $this->setAdditionalProperty('reminder_message', $value);

      return $this;
    } //setReminderMessage

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'additional_message' => $this->getReminderMessage(),
      );
    } // getAdditionalTemplateVars

    // ---------------------------------------------------
    //  Delivery system
    // ---------------------------------------------------

    /**
     * This notification should not be displayed in web interface
     *
     * @param NotificationChannel $channel
     * @param IUser $recipient
     * @return bool
     */
    function isThisNotificationVisibleInChannel(NotificationChannel $channel, IUser $recipient) {
      if($channel instanceof EmailNotificationChannel) {
        return true; // Always deliver notifications via email
      } // if

      return parent::isThisNotificationVisibleInChannel($channel, $recipient);
    } // isThisNotificationVisibleInChannel

  }