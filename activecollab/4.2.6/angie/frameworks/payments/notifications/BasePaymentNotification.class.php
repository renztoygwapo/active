<?php

  /**
   * Base notification for payment notification
   *
   * @package angie.frameworks.payments
   * @subpackage notifications
   */
  abstract class BasePaymentNotification extends Notification {

    /**
     * Return parent payment
     *
     * @return Payment
     */
    function getPayment() {
      return Payments::findById($this->getAdditionalProperty('payment_id'));
    } // getPayment

    /**
     * Set parent payment instance
     *
     * @param Payment $payment
     * @return BasePaymentNotification
     */
    function &setPayment(Payment $payment) {
      $this->setAdditionalProperty('payment_id', $payment->getId());

      return $this;
    } // setPayment

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'payment' => $this->getPayment(),
      );
    } // getAdditionalTemplateVars

  }