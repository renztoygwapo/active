<?php

/**
 * Paypal express checkout payment class
 * 
 * @package angie.frameworks.payments
 * @subpackage models.payments
 * 
 */
class PaypalExpressCheckoutPayment extends PaypalPayment {


  /**
   * Return "redirect" url for this payment
   *
   */
  function getRedirectUrl() {
    $redirect_url = $this->getGateway()->getGoLive() ? PaypalGateway::REDIRECT_URL : PaypalGateway::TEST_REDIRECT_URL;
    return $redirect_url . "&token=" . $this->getToken();
  } //getReturnUrl

  /**
   * Set Token
   *
   * @param $token
   * @return mixed
   */
  function setToken($token) {
    return $this->setAdditionalProperty('TOKEN',$token);
  }//setToken
  
  /**
   * Return token from additional parameters
   * 
   * @return string
   */
  function getToken() {
    return $this->getAdditionalProperty('TOKEN');
  }//getToken
  
  
}