<?php

  /**
   * Invoice canceled notification
   *
   * @package activeCollab.modules.invoicing
   * @subpackage notifications
   */
  class InvoiceCanceledNotification extends InvoiceStatusChangedNotification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("Invoice ':num' has been Canceled", array(
        'num' => $this->getParent() instanceof Invoice ? $this->getParent()->getName() : '',
      ), true, $user->getLanguage());
    } // getMessage

  }