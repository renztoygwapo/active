<?php

  /**
   * BasePayment class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BasePayment extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'payments';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'type', 'parent_type', 'parent_id', 'amount', 'currency_id', 'gateway_type', 'gateway_id', 'status', 'reason', 'reason_text', 'created_by_id', 'created_by_name', 'created_by_email', 'created_on', 'paid_on', 'comment', 'method', 'raw_additional_properties');
    
    /**
     * Primary key fields
     *
     * @var array
     */
    protected $primary_key = array('id');

    /**
     * Return name of this model
     *
     * @param boolean $underscore
     * @param boolean $singular
     * @return string
     */
    function getModelName($underscore = false, $singular = false) {
      if($singular) {
        return $underscore ? 'payment' : 'Payment';
      } else {
        return $underscore ? 'payments' : 'Payments';
      } // if
    } // getModelName

    /**
     * Name of AI field (if any)
     *
     * @var string
     */
    protected $auto_increment = 'id';
    // ---------------------------------------------------
    //  Fields
    // ---------------------------------------------------

    /**
     * Return value of id field
     *
     * @return integer
     */
    function getId() {
      return $this->getFieldValue('id');
    } // getId
    
    /**
     * Set value of id field
     *
     * @param integer $value
     * @return integer
     */
    function setId($value) {
      return $this->setFieldValue('id', $value);
    } // setId

    /**
     * Return value of type field
     *
     * @return string
     */
    function getType() {
      return $this->getFieldValue('type');
    } // getType
    
    /**
     * Set value of type field
     *
     * @param string $value
     * @return string
     */
    function setType($value) {
      return $this->setFieldValue('type', $value);
    } // setType

    /**
     * Return value of parent_type field
     *
     * @return string
     */
    function getParentType() {
      return $this->getFieldValue('parent_type');
    } // getParentType
    
    /**
     * Set value of parent_type field
     *
     * @param string $value
     * @return string
     */
    function setParentType($value) {
      return $this->setFieldValue('parent_type', $value);
    } // setParentType

    /**
     * Return value of parent_id field
     *
     * @return integer
     */
    function getParentId() {
      return $this->getFieldValue('parent_id');
    } // getParentId
    
    /**
     * Set value of parent_id field
     *
     * @param integer $value
     * @return integer
     */
    function setParentId($value) {
      return $this->setFieldValue('parent_id', $value);
    } // setParentId

    /**
     * Return value of amount field
     *
     * @return float
     */
    function getAmount() {
      return $this->getFieldValue('amount');
    } // getAmount
    
    /**
     * Set value of amount field
     *
     * @param float $value
     * @return float
     */
    function setAmount($value) {
      return $this->setFieldValue('amount', $value);
    } // setAmount

    /**
     * Return value of currency_id field
     *
     * @return integer
     */
    function getCurrencyId() {
      return $this->getFieldValue('currency_id');
    } // getCurrencyId
    
    /**
     * Set value of currency_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCurrencyId($value) {
      return $this->setFieldValue('currency_id', $value);
    } // setCurrencyId

    /**
     * Return value of gateway_type field
     *
     * @return string
     */
    function getGatewayType() {
      return $this->getFieldValue('gateway_type');
    } // getGatewayType
    
    /**
     * Set value of gateway_type field
     *
     * @param string $value
     * @return string
     */
    function setGatewayType($value) {
      return $this->setFieldValue('gateway_type', $value);
    } // setGatewayType

    /**
     * Return value of gateway_id field
     *
     * @return integer
     */
    function getGatewayId() {
      return $this->getFieldValue('gateway_id');
    } // getGatewayId
    
    /**
     * Set value of gateway_id field
     *
     * @param integer $value
     * @return integer
     */
    function setGatewayId($value) {
      return $this->setFieldValue('gateway_id', $value);
    } // setGatewayId

    /**
     * Return value of status field
     *
     * @return string
     */
    function getStatus() {
      return $this->getFieldValue('status');
    } // getStatus
    
    /**
     * Set value of status field
     *
     * @param string $value
     * @return string
     */
    function setStatus($value) {
      return $this->setFieldValue('status', $value);
    } // setStatus

    /**
     * Return value of reason field
     *
     * @return string
     */
    function getReason() {
      return $this->getFieldValue('reason');
    } // getReason
    
    /**
     * Set value of reason field
     *
     * @param string $value
     * @return string
     */
    function setReason($value) {
      return $this->setFieldValue('reason', $value);
    } // setReason

    /**
     * Return value of reason_text field
     *
     * @return string
     */
    function getReasonText() {
      return $this->getFieldValue('reason_text');
    } // getReasonText
    
    /**
     * Set value of reason_text field
     *
     * @param string $value
     * @return string
     */
    function setReasonText($value) {
      return $this->setFieldValue('reason_text', $value);
    } // setReasonText

    /**
     * Return value of created_by_id field
     *
     * @return integer
     */
    function getCreatedById() {
      return $this->getFieldValue('created_by_id');
    } // getCreatedById
    
    /**
     * Set value of created_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCreatedById($value) {
      return $this->setFieldValue('created_by_id', $value);
    } // setCreatedById

    /**
     * Return value of created_by_name field
     *
     * @return string
     */
    function getCreatedByName() {
      return $this->getFieldValue('created_by_name');
    } // getCreatedByName
    
    /**
     * Set value of created_by_name field
     *
     * @param string $value
     * @return string
     */
    function setCreatedByName($value) {
      return $this->setFieldValue('created_by_name', $value);
    } // setCreatedByName

    /**
     * Return value of created_by_email field
     *
     * @return string
     */
    function getCreatedByEmail() {
      return $this->getFieldValue('created_by_email');
    } // getCreatedByEmail
    
    /**
     * Set value of created_by_email field
     *
     * @param string $value
     * @return string
     */
    function setCreatedByEmail($value) {
      return $this->setFieldValue('created_by_email', $value);
    } // setCreatedByEmail

    /**
     * Return value of created_on field
     *
     * @return DateTimeValue
     */
    function getCreatedOn() {
      return $this->getFieldValue('created_on');
    } // getCreatedOn
    
    /**
     * Set value of created_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setCreatedOn($value) {
      return $this->setFieldValue('created_on', $value);
    } // setCreatedOn

    /**
     * Return value of paid_on field
     *
     * @return DateValue
     */
    function getPaidOn() {
      return $this->getFieldValue('paid_on');
    } // getPaidOn
    
    /**
     * Set value of paid_on field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setPaidOn($value) {
      return $this->setFieldValue('paid_on', $value);
    } // setPaidOn

    /**
     * Return value of comment field
     *
     * @return string
     */
    function getComment() {
      return $this->getFieldValue('comment');
    } // getComment
    
    /**
     * Set value of comment field
     *
     * @param string $value
     * @return string
     */
    function setComment($value) {
      return $this->setFieldValue('comment', $value);
    } // setComment

    /**
     * Return value of method field
     *
     * @return string
     */
    function getMethod() {
      return $this->getFieldValue('method');
    } // getMethod
    
    /**
     * Set value of method field
     *
     * @param string $value
     * @return string
     */
    function setMethod($value) {
      return $this->setFieldValue('method', $value);
    } // setMethod

    /**
     * Return value of raw_additional_properties field
     *
     * @return string
     */
    function getRawAdditionalProperties() {
      return $this->getFieldValue('raw_additional_properties');
    } // getRawAdditionalProperties
    
    /**
     * Set value of raw_additional_properties field
     *
     * @param string $value
     * @return string
     */
    function setRawAdditionalProperties($value) {
      return $this->setFieldValue('raw_additional_properties', $value);
    } // setRawAdditionalProperties

    /**
     * Set value of specific field
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @throws InvalidParamError
     */
    function setFieldValue($name, $value) {
      $real_name = $this->realFieldName($name);

      if($value === null) {
        return parent::setFieldValue($real_name, null);
      } else {
        switch($real_name) {
          case 'id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'parent_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'parent_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'amount':
            return parent::setFieldValue($real_name, (float) $value);
          case 'currency_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'gateway_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'gateway_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'status':
            return parent::setFieldValue($real_name, (empty($value) ? NULL : (string) $value));
          case 'reason':
            return parent::setFieldValue($real_name, (empty($value) ? NULL : (string) $value));
          case 'reason_text':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'created_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'paid_on':
            return parent::setFieldValue($real_name, dateval($value));
          case 'comment':
            return parent::setFieldValue($real_name, (string) $value);
          case 'method':
            return parent::setFieldValue($real_name, (string) $value);
          case 'raw_additional_properties':
            return parent::setFieldValue($real_name, (string) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }