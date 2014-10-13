<?php

  /**
   * Quote updated notification
   *
   * @package activeCollab.modules.invoicing
   * @subpackage notifications
   */
  class QuoteUpdatedNotification extends QuoteNotification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("Quote ':name' has been Updated", array(
        'name' => $this->getParent() instanceof Quote ? $this->getParent()->getName() : '',
      ), true, $user->getLanguage());
    } // getMessage

    /**
     * Return attachments
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAttachments(NotificationChannel $channel) {
      if($channel instanceof EmailNotificationChannel) {
        $filename_name = 'quote_' . $this->getParent()->getId() . '.pdf';
        $filename = WORK_PATH . '/' . $filename_name;

        require_once INVOICING_MODULE_PATH . '/models/InvoicePDFGenerator.class.php';
        InvoicePDFGenerator::save($this->getParent(), $filename);

        return array($filename);
      } // if

      return parent::getAttachments($channel);
    } // getAttachments

  }