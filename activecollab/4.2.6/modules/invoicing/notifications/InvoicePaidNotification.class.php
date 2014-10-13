<?php

  /**
   * Invoice paid notification
   *
   * @package activeCollab.modules.invoicing
   * @subpackage notifications
   */
  class InvoicePaidNotification extends InvoiceStatusChangedNotification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("Invoice ':num' has been Paid", array(
        'num' => $this->getParent() instanceof Invoice ? $this->getParent()->getName() : '',
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

  }