<?php

  /**
   * New payment for payer notification
   *
   * @package angie.frameworks.payments
   * @subpackage notifications
   */
   class NewPaymentToPayerNotification extends BasePaymentNotification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang('Thank You for Your Payment', null, true, $user->getLanguage());
    } // getMessage

  }