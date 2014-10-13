<?php

  /**
   * Invoice issued notification
   *
   * @package activeCollab.modules.invoicing
   * @subpackage notifications
   */
  class InvoiceIssuedNotification extends InvoiceStatusChangedNotification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("Invoice ':name' has been Issued", array(
        'name' => $this->getParent() instanceof Invoice ? $this->getParent()->getName() : '',
      ), true, $user->getLanguage());
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

    // ---------------------------------------------------
    //  Delivery
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