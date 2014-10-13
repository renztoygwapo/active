<?php

  /**
   * Framework level payment instance implementation
   *
   * @package angie.frameworks.payments
   * @subpackage models
   */
  abstract class FwPayment extends BasePayment implements IRoutingContext {

    // Allow payments flag
    const ALLOW_PARTIAL = 2;
    const ALLOW_FULL = 1;
    const DO_NOT_ALLOW = 0;
    const USE_SYSTEM_DEFAULT = -1;

    const STATUS_PAID = 'Paid';
    const STATUS_PENDING = 'Pending';
    const STATUS_DELETED = 'Deleted';
    const STATUS_CANCELED = 'Canceled';

    const REASON_FRAUD = 'Fraud';
    const REASON_REFUND = 'Refund';
    const REASON_OTHER = 'Other';

    /**
     * Response from service
     *
     * @var array
     */
    var $response;

    /**
     * Is error occurred in payment proccess
     *
     * @var boolean
     */
    var $is_error;

    /**
     * Error message
     *
     * @var string
     */
    var $error_message;


    /**
     * Return is_error flag
     *
     * @return boolean
     */
    function getIsError() {
      return $this->is_error;
    }//getIsError

    /**
     * Set is_error flag
     *
     * @return boolean
     */
    function setIsError($value) {
      return $this->is_error = $value;
    }//setIsError

    /**
     * Return error_message flag
     *
     * @return string
     */
    function getErrorMessage() {
      return $this->error_message;
    }//getErrorMessage

    /**
     * Set is_error flag
     *
     * @return string
     */
    function setErrorMessage($value) {
      return $this->error_message = $value;
    }//setErrorMessage

    /**
     * Get is_public
     */
    public function getIsPublic() {
      return (boolean) $this->getAdditionalProperty('is_public');
    } //getIsPublic

    /**
     * Set is_public to additional parameters
     * @param boolean $value
     */
    public function setIsPublic($value) {
      $this->setAdditionalProperty('is_public', boolval($value));
    } //setIsPublic

    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = parent::describe($user, $detailed, $for_interface);

      $result['name'] = $this->getGateway()->getGatewayName();

      if($this instanceof PaypalExpressCheckoutPayment) {
        $result['redirect_url'] = $this->getRedirectUrl();
        $result['token'] = $this->getToken();
      }//if

      $result['invoice'] = $detailed ? $this->getParent()->describe($user, $detailed, $for_interface) : $this->getParent();

      $result['paid_on'] = $this->getPaidOn();
      $result['gateway_icon'] = $this->getGateway()->getIconPath();
      $result['comment'] = $this->getComment();
      $result['status'] = $this->getStatus();
      $result['amount'] = $this->getAmount();
      $result['tax'] = $this->getTaxAmount() ? $this->getTaxAmount() : 0;
      $result['currency'] = $this->getCurrency();
      $result['method'] = $this->getMethod() ? $this->getMethod() : lang('Unknown');
      $result['is_deleted'] = $this->getStatus() == Payment::STATUS_DELETED ? true : false;

      $result['total'] = $this->getParent()->payments()->getPaidAmount();
      $result['total_percent'] = $this->getParent()->payments()->getPercentPaid();

      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      $result = parent::describeForApi($user, $detailed);

      $result['name'] = $this->getGateway()->getGatewayName();

      if($this->getGateway() instanceof PaypalExpressCheckoutGateway) {
        $redirect_url = $this->getGateway()->getGoLive() ? PaypalGateway::REDIRECT_URL : PaypalGateway::TEST_REDIRECT_URL;
        $result['redirect_url'] = $redirect_url . "&token=" . $this->getToken();
        $result['token'] = $this->getToken();
      }//if

      $result['invoice'] = $this->getParent()->describeForApi($user, false);

      $result['paid_on'] = $this->getPaidOn();
      $result['gateway_icon'] = $this->getGateway()->getIconPath();
      $result['comment'] = $this->getComment();
      $result['status'] = $this->getStatus();
      $result['amount'] = $this->getAmount();
      $result['tax'] = $this->getTaxAmount() ? $this->getTaxAmount() : 0;
      $result['currency'] = $this->getCurrency();

      $result['is_deleted'] = $this->getStatus() == Payment::STATUS_DELETED ? true : false;

      $result['total'] = $this->getParent()->payments()->getPaidAmount();
      $result['total_percent'] = $this->getParent()->payments()->getPercentPaid();

      return $result;
    } // describeForApi

    /**
     * Return true if this payment has status paid
     *
     * @return boolean
     */
    function isPaid() {
      return $this->getStatus() == Payment::STATUS_PAID;
    }//isPaid

    /**
     * Return edit payment URL
     *
     * @return string
     */
    function getEditUrl() {
      $params = $this->getRoutingContextParams();
      return Router::assemble($this->getParent()->getRoutingContext() . '_payment_edit', $params);
    } // getEditUrl

    /**
     * Return delete payment URL
     *
     * @return string
     */
    function getDeleteUrl() {
      $params = $this->getRoutingContextParams();
      return Router::assemble($this->getParent()->getRoutingContext() . '_payment_delete', $params);
    }//getDeleteUrl

    /**
     * Return view payment URL
     *
     * @return string
     */
    function getViewUrl() {
      $params = $this->getRoutingContextParams();
      return Router::assemble($this->getParent()->getRoutingContext() . '_payment', $params);
    }//getViewUrl

    /**
     * Return payment currency
     *
     * @return Currency
     */
    function getCurrency() {
      return Currencies::findById($this->getCurrencyId());
    } // getCurrency

    /**
     * Return payment gateway which used to manage payment
     *
     * @return PaymentGateway
     */
    function getGateway() {
      if($this->getGatewayId() == 0) {
        $gateway_type = $this->getGatewayType();
        return new $gateway_type();
      } else {
        return PaymentGateways::findById($this->getGatewayId());
      }//if
    } // getGateway

    /**
     * Set payment gateway which used to manage payment
     *
     * @return PaymentGateway
     */
    function setGateway(PaymentGateway $gateway = null) {
      if($gateway instanceof PaymentGateway) {
        $this->setGatewayId($gateway->getId());
        $this->setGatewayType($gateway->getType());
      }//if
    } // getGateway

    /**
     * Get Additional note from rae additional properties
     */
    public function getNote() {
      return $this->getAdditionalProperty('note');
    } //getNote

    /**
     * Set note to additional parameters
     * @param unknown_type $value
     */
    public function setNote($value) {
      $this->setAdditionalProperty('note',$value);
    } //set note

    /**
     * @return the $short_message
     */
    public function getShortMessage() {
      return $this->getAdditionalProperty('short_message');
    } //getShortMessage

    /**
     * @param $short_message the $short_message to set
     */
    public function setShortMessage($value) {
      $this->setAdditionalProperty('short_message',$value);
    } //setShortMessage

    /**
     * @return the $long_message
     */
    public function getLongMessage() {
      return $this->getAdditionalProperty('long_message');
    } //getLongMessage

    /**
     * @param $long_message the $long_message to set
     */
    public function setLongMessage($value) {
      $this->setAdditionalProperty('long_message',$value);
    } //setLongMessage

    /**
     * @return the $error_code
     */
    public function getErrorCode() {
      return $this->getAdditionalProperty('error_code');
    } //getErrorCode

    /**
     * @param $error_code the $error_code to set
     */
    public function setErrorCode($value) {
      $this->setAdditionalProperty('error_code',$value);
    } //setErrorCode

    /**
     * @return the $severity_code
     */
    public function getSeverityCode() {
      return $this->getAdditionalProperty('severity_code');
    } //getSeverityCode

    /**
     * @param $severity_code the $severity_code to set
     */
    public function setSeverityCode($value) {
      $this->setAdditionalProperty('severity_code',$value);
    } //setSeverityCode

    /**
     * @return the $timestamp
     */
    public function getTimestamp() {
      return $this->getAdditionalProperty('timestamp');
    } //getTimestamp

    /**
     * @param $timestamp the $timestamp to set
     */
    public function setTimestamp($value) {
      $this->setAdditionalProperty('timestamp',$value);
    } //setTimestamp

    /**
     * @return the $corelation_id
     */
    public function getCorelationId() {
      return $this->getAdditionalProperty('corelation_id');
    } //getCorelationId

    /**
     * @param $corelation_id the $corelation_id to set
     */
    public function setCorelationId($value) {
      $this->setAdditionalProperty('corelation_id',$value);
    } //setCorelationId

    /**
     * @return the $transaction_id
     */
    public function getTransactionId() {
      return $this->getAdditionalProperty('transaction_id');
    } //getTransactionId

    /**
     * @param $transaction_id the $transaction_id to set
     */
    public function setTransactionId($value) {
      $this->setAdditionalProperty('transaction_id',$value);
    } //setTransactionId

    /**
     * @return the build
     */
    public function getBuild() {
      return $this->getAdditionalProperty('build');
    } //getBuild

    /**
     * @param $value the $build to set
     */
    public function setBuild($value) {
      $this->setAdditionalProperty('build',$value);
    } //setBuild

    /**
     * @return the avs_code
     */
    public function getAvsCode() {
      return $this->getAdditionalProperty('avs_code');
    } //getAvsCode

    /**
     * @param $value the avs_code to set
     */
    public function setAvsCode($value) {
      $this->setAdditionalProperty('avs_code',$value);
    } //setAvsCode


    /**
     * @return the cvv2_match
     */
    public function getCvv2Match() {
      return $this->getAdditionalProperty('cvv2_match');
    } //getCvv2Match

    /**
     * @param $value the cvv2_match to set
     */
    public function setCvv2Match($value) {
      $this->setAdditionalProperty('cvv2_match',$value);
    } //setCvv2Match

    /**
     * @return the $version
     */
    public function getVersion() {
      return $this->getAdditionalProperty('version');
    } //getVersion

    /**
     * @param $version the $version to set
     */
    public function setVersion($value) {
      $this->setAdditionalProperty('version',$value);
    } //setVersion

    /**
     * @return the payment_gateway_id
     */
    public function getPayerId() {
      return $this->getAdditionalProperty('payer_id');
    } //getPayerId

    /**
     * @param payment_gateway_id
     */
    public function setPayerId($value) {
      $this->setAdditionalProperty('payer_id',$value);
    } //setPayerId

    /**
     * @return the tax_amount
     */
    public function getTaxAmount() {
      return $this->getAdditionalProperty('tax_amount');
    } //getTaxAmount

    /**
     * @param tax_amount
     */
    public function setTaxAmount($value) {
      $this->setAdditionalProperty('tax_amount',$value);
    } //setTaxAmount


    // ---------------------------------------------------
    //  Interfaces implementation
    // ---------------------------------------------------

    /**
     * Routing context name
     *
     * @var string
     */
    private $routing_context = false;

    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      if($this->routing_context === false) {
        $this->routing_context = $this->getParent()->getRoutingContext() . '_payment';
      } // if

      return $this->routing_context;
    } // getRoutingContext

    /**
     * Routing context parameters
     *
     * @var array
     */
    private $routing_context_params = false;

    /**
     * Return routing context parameters
     *
     * @return array
     */
    function getRoutingContextParams() {
      if($this->routing_context_params === false) {
        $parent_params = $this->getParent()->getRoutingContextParams();

        $additional_params = array('payment_id' => $this->getId());
        if(method_exists($this->getParent(), 'getCompanyId')) {
          $additional_params['company_id'] = $this->getParent()->getCompanyId();
        }//if
        $this->routing_context_params = is_array($parent_params) ? array_merge($parent_params, $additional_params) : $additional_params;
      } // if

      return $this->routing_context_params;
    } // getRoutingContextParams


    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if($this->getAmount() < 0.01) {
        $errors->addError(lang('Minumum value for your payment amount is 0.01'), 'amount');
      } // if
    } // validate

  }