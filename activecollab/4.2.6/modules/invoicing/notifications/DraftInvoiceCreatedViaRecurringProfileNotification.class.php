<?php

  /**
   * Draft invoice created via recurring profile notification
   *
   * @package activeCollab.modules.invoicing
   * @subpackage notification
   */
  class DraftInvoiceCreatedViaRecurringProfileNotification extends RecurringProfileNotification {

    /**
     * Return notification mesasge
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang('":name" Profile Created a Draft Invoice', array(
        'name' => $this->getProfile() instanceof RecurringProfile ? $this->getProfile()->getName() : '',
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