<?php

  /**
   * New payment notification
   *
   * @package angie.frameworks.payments
   * @subpackage notifications
   */
  class NewPaymentNotification extends BasePaymentNotification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang('Payment has been Received', null, true, $user->getLanguage());
    } // getMessage

    /**
     * Returns true if this notification is visible to $user
     *
     * @param IUser $user
     * @return bool
     */
    function isThisNotificationVisibleToUser(IUser $user) {
      $payment = $this->getPayment();

      if($payment instanceof CustomPayment && $user instanceof User && $user->is($payment->getCreatedBy())) {
        return false;
      } // if

      return parent::isThisNotificationVisibleToUser($user);
    } // isThisNotificationVisibleToUser

  }