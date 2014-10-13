<?php

  /**
   * BaseInvoiceObject class
   *
   * @package ActiveCollab.modules.invoicing
   * @subpackage models
   */
  abstract class BaseInvoiceObject extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'invoice_objects';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'type', 'company_id', 'company_name', 'company_address', 'currency_id', 'language_id', 'project_id', 'name', 'subtotal', 'tax', 'total', 'balance_due', 'paid_amount', 'note', 'private_note', 'status', 'based_on_type', 'based_on_id', 'allow_payments', 'second_tax_is_enabled', 'second_tax_is_compound', 'state', 'original_state', 'visibility', 'original_visibility', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'recipient_id', 'recipient_name', 'recipient_email', 'sent_on', 'sent_by_id', 'sent_by_name', 'sent_by_email', 'reminder_sent_on', 'closed_on', 'closed_by_id', 'closed_by_name', 'closed_by_email', 'varchar_field_1', 'varchar_field_2', 'varchar_field_3', 'varchar_field_4', 'integer_field_1', 'integer_field_2', 'integer_field_3', 'date_field_1', 'date_field_2', 'date_field_3', 'datetime_field_1', 'hash');
    
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
        return $underscore ? 'invoice_object' : 'InvoiceObject';
      } else {
        return $underscore ? 'invoice_objects' : 'InvoiceObjects';
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
     * Return value of company_id field
     *
     * @return integer
     */
    function getCompanyId() {
      return $this->getFieldValue('company_id');
    } // getCompanyId
    
    /**
     * Set value of company_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCompanyId($value) {
      return $this->setFieldValue('company_id', $value);
    } // setCompanyId

    /**
     * Return value of company_name field
     *
     * @return string
     */
    function getCompanyName() {
      return $this->getFieldValue('company_name');
    } // getCompanyName
    
    /**
     * Set value of company_name field
     *
     * @param string $value
     * @return string
     */
    function setCompanyName($value) {
      return $this->setFieldValue('company_name', $value);
    } // setCompanyName

    /**
     * Return value of company_address field
     *
     * @return string
     */
    function getCompanyAddress() {
      return $this->getFieldValue('company_address');
    } // getCompanyAddress
    
    /**
     * Set value of company_address field
     *
     * @param string $value
     * @return string
     */
    function setCompanyAddress($value) {
      return $this->setFieldValue('company_address', $value);
    } // setCompanyAddress

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
     * Return value of language_id field
     *
     * @return integer
     */
    function getLanguageId() {
      return $this->getFieldValue('language_id');
    } // getLanguageId
    
    /**
     * Set value of language_id field
     *
     * @param integer $value
     * @return integer
     */
    function setLanguageId($value) {
      return $this->setFieldValue('language_id', $value);
    } // setLanguageId

    /**
     * Return value of project_id field
     *
     * @return integer
     */
    function getProjectId() {
      return $this->getFieldValue('project_id');
    } // getProjectId
    
    /**
     * Set value of project_id field
     *
     * @param integer $value
     * @return integer
     */
    function setProjectId($value) {
      return $this->setFieldValue('project_id', $value);
    } // setProjectId

    /**
     * Return value of name field
     *
     * @return string
     */
    function getName() {
      return $this->getFieldValue('name');
    } // getName
    
    /**
     * Set value of name field
     *
     * @param string $value
     * @return string
     */
    function setName($value) {
      return $this->setFieldValue('name', $value);
    } // setName

    /**
     * Return value of subtotal field
     *
     * @return float
     */
    function getSubtotal() {
      return $this->getFieldValue('subtotal');
    } // getSubtotal
    
    /**
     * Set value of subtotal field
     *
     * @param float $value
     * @return float
     */
    function setSubtotal($value) {
      return $this->setFieldValue('subtotal', $value);
    } // setSubtotal

    /**
     * Return value of tax field
     *
     * @return float
     */
    function getTax() {
      return $this->getFieldValue('tax');
    } // getTax
    
    /**
     * Set value of tax field
     *
     * @param float $value
     * @return float
     */
    function setTax($value) {
      return $this->setFieldValue('tax', $value);
    } // setTax

    /**
     * Return value of total field
     *
     * @return float
     */
    function getTotal() {
      return $this->getFieldValue('total');
    } // getTotal
    
    /**
     * Set value of total field
     *
     * @param float $value
     * @return float
     */
    function setTotal($value) {
      return $this->setFieldValue('total', $value);
    } // setTotal

    /**
     * Return value of balance_due field
     *
     * @return float
     */
    function getBalanceDue() {
      return $this->getFieldValue('balance_due');
    } // getBalanceDue
    
    /**
     * Set value of balance_due field
     *
     * @param float $value
     * @return float
     */
    function setBalanceDue($value) {
      return $this->setFieldValue('balance_due', $value);
    } // setBalanceDue

    /**
     * Return value of paid_amount field
     *
     * @return float
     */
    function getPaidAmount() {
      return $this->getFieldValue('paid_amount');
    } // getPaidAmount
    
    /**
     * Set value of paid_amount field
     *
     * @param float $value
     * @return float
     */
    function setPaidAmount($value) {
      return $this->setFieldValue('paid_amount', $value);
    } // setPaidAmount

    /**
     * Return value of note field
     *
     * @return string
     */
    function getNote() {
      return $this->getFieldValue('note');
    } // getNote
    
    /**
     * Set value of note field
     *
     * @param string $value
     * @return string
     */
    function setNote($value) {
      return $this->setFieldValue('note', $value);
    } // setNote

    /**
     * Return value of private_note field
     *
     * @return string
     */
    function getPrivateNote() {
      return $this->getFieldValue('private_note');
    } // getPrivateNote
    
    /**
     * Set value of private_note field
     *
     * @param string $value
     * @return string
     */
    function setPrivateNote($value) {
      return $this->setFieldValue('private_note', $value);
    } // setPrivateNote

    /**
     * Return value of status field
     *
     * @return integer
     */
    function getStatus() {
      return $this->getFieldValue('status');
    } // getStatus
    
    /**
     * Set value of status field
     *
     * @param integer $value
     * @return integer
     */
    function setStatus($value) {
      return $this->setFieldValue('status', $value);
    } // setStatus

    /**
     * Return value of based_on_type field
     *
     * @return string
     */
    function getBasedOnType() {
      return $this->getFieldValue('based_on_type');
    } // getBasedOnType
    
    /**
     * Set value of based_on_type field
     *
     * @param string $value
     * @return string
     */
    function setBasedOnType($value) {
      return $this->setFieldValue('based_on_type', $value);
    } // setBasedOnType

    /**
     * Return value of based_on_id field
     *
     * @return integer
     */
    function getBasedOnId() {
      return $this->getFieldValue('based_on_id');
    } // getBasedOnId
    
    /**
     * Set value of based_on_id field
     *
     * @param integer $value
     * @return integer
     */
    function setBasedOnId($value) {
      return $this->setFieldValue('based_on_id', $value);
    } // setBasedOnId

    /**
     * Return value of allow_payments field
     *
     * @return integer
     */
    function getAllowPayments() {
      return $this->getFieldValue('allow_payments');
    } // getAllowPayments
    
    /**
     * Set value of allow_payments field
     *
     * @param integer $value
     * @return integer
     */
    function setAllowPayments($value) {
      return $this->setFieldValue('allow_payments', $value);
    } // setAllowPayments

    /**
     * Return value of second_tax_is_enabled field
     *
     * @return boolean
     */
    function getSecondTaxIsEnabled() {
      return $this->getFieldValue('second_tax_is_enabled');
    } // getSecondTaxIsEnabled
    
    /**
     * Set value of second_tax_is_enabled field
     *
     * @param boolean $value
     * @return boolean
     */
    function setSecondTaxIsEnabled($value) {
      return $this->setFieldValue('second_tax_is_enabled', $value);
    } // setSecondTaxIsEnabled

    /**
     * Return value of second_tax_is_compound field
     *
     * @return boolean
     */
    function getSecondTaxIsCompound() {
      return $this->getFieldValue('second_tax_is_compound');
    } // getSecondTaxIsCompound
    
    /**
     * Set value of second_tax_is_compound field
     *
     * @param boolean $value
     * @return boolean
     */
    function setSecondTaxIsCompound($value) {
      return $this->setFieldValue('second_tax_is_compound', $value);
    } // setSecondTaxIsCompound

    /**
     * Return value of state field
     *
     * @return integer
     */
    function getState() {
      return $this->getFieldValue('state');
    } // getState
    
    /**
     * Set value of state field
     *
     * @param integer $value
     * @return integer
     */
    function setState($value) {
      return $this->setFieldValue('state', $value);
    } // setState

    /**
     * Return value of original_state field
     *
     * @return integer
     */
    function getOriginalState() {
      return $this->getFieldValue('original_state');
    } // getOriginalState
    
    /**
     * Set value of original_state field
     *
     * @param integer $value
     * @return integer
     */
    function setOriginalState($value) {
      return $this->setFieldValue('original_state', $value);
    } // setOriginalState

    /**
     * Return value of visibility field
     *
     * @return integer
     */
    function getVisibility() {
      return $this->getFieldValue('visibility');
    } // getVisibility
    
    /**
     * Set value of visibility field
     *
     * @param integer $value
     * @return integer
     */
    function setVisibility($value) {
      return $this->setFieldValue('visibility', $value);
    } // setVisibility

    /**
     * Return value of original_visibility field
     *
     * @return integer
     */
    function getOriginalVisibility() {
      return $this->getFieldValue('original_visibility');
    } // getOriginalVisibility
    
    /**
     * Set value of original_visibility field
     *
     * @param integer $value
     * @return integer
     */
    function setOriginalVisibility($value) {
      return $this->setFieldValue('original_visibility', $value);
    } // setOriginalVisibility

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
     * Return value of recipient_id field
     *
     * @return integer
     */
    function getRecipientId() {
      return $this->getFieldValue('recipient_id');
    } // getRecipientId
    
    /**
     * Set value of recipient_id field
     *
     * @param integer $value
     * @return integer
     */
    function setRecipientId($value) {
      return $this->setFieldValue('recipient_id', $value);
    } // setRecipientId

    /**
     * Return value of recipient_name field
     *
     * @return string
     */
    function getRecipientName() {
      return $this->getFieldValue('recipient_name');
    } // getRecipientName
    
    /**
     * Set value of recipient_name field
     *
     * @param string $value
     * @return string
     */
    function setRecipientName($value) {
      return $this->setFieldValue('recipient_name', $value);
    } // setRecipientName

    /**
     * Return value of recipient_email field
     *
     * @return string
     */
    function getRecipientEmail() {
      return $this->getFieldValue('recipient_email');
    } // getRecipientEmail
    
    /**
     * Set value of recipient_email field
     *
     * @param string $value
     * @return string
     */
    function setRecipientEmail($value) {
      return $this->setFieldValue('recipient_email', $value);
    } // setRecipientEmail

    /**
     * Return value of sent_on field
     *
     * @return DateTimeValue
     */
    function getSentOn() {
      return $this->getFieldValue('sent_on');
    } // getSentOn
    
    /**
     * Set value of sent_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setSentOn($value) {
      return $this->setFieldValue('sent_on', $value);
    } // setSentOn

    /**
     * Return value of sent_by_id field
     *
     * @return integer
     */
    function getSentById() {
      return $this->getFieldValue('sent_by_id');
    } // getSentById
    
    /**
     * Set value of sent_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setSentById($value) {
      return $this->setFieldValue('sent_by_id', $value);
    } // setSentById

    /**
     * Return value of sent_by_name field
     *
     * @return string
     */
    function getSentByName() {
      return $this->getFieldValue('sent_by_name');
    } // getSentByName
    
    /**
     * Set value of sent_by_name field
     *
     * @param string $value
     * @return string
     */
    function setSentByName($value) {
      return $this->setFieldValue('sent_by_name', $value);
    } // setSentByName

    /**
     * Return value of sent_by_email field
     *
     * @return string
     */
    function getSentByEmail() {
      return $this->getFieldValue('sent_by_email');
    } // getSentByEmail
    
    /**
     * Set value of sent_by_email field
     *
     * @param string $value
     * @return string
     */
    function setSentByEmail($value) {
      return $this->setFieldValue('sent_by_email', $value);
    } // setSentByEmail

    /**
     * Return value of reminder_sent_on field
     *
     * @return DateTimeValue
     */
    function getReminderSentOn() {
      return $this->getFieldValue('reminder_sent_on');
    } // getReminderSentOn
    
    /**
     * Set value of reminder_sent_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setReminderSentOn($value) {
      return $this->setFieldValue('reminder_sent_on', $value);
    } // setReminderSentOn

    /**
     * Return value of closed_on field
     *
     * @return DateTimeValue
     */
    function getClosedOn() {
      return $this->getFieldValue('closed_on');
    } // getClosedOn
    
    /**
     * Set value of closed_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setClosedOn($value) {
      return $this->setFieldValue('closed_on', $value);
    } // setClosedOn

    /**
     * Return value of closed_by_id field
     *
     * @return integer
     */
    function getClosedById() {
      return $this->getFieldValue('closed_by_id');
    } // getClosedById
    
    /**
     * Set value of closed_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setClosedById($value) {
      return $this->setFieldValue('closed_by_id', $value);
    } // setClosedById

    /**
     * Return value of closed_by_name field
     *
     * @return string
     */
    function getClosedByName() {
      return $this->getFieldValue('closed_by_name');
    } // getClosedByName
    
    /**
     * Set value of closed_by_name field
     *
     * @param string $value
     * @return string
     */
    function setClosedByName($value) {
      return $this->setFieldValue('closed_by_name', $value);
    } // setClosedByName

    /**
     * Return value of closed_by_email field
     *
     * @return string
     */
    function getClosedByEmail() {
      return $this->getFieldValue('closed_by_email');
    } // getClosedByEmail
    
    /**
     * Set value of closed_by_email field
     *
     * @param string $value
     * @return string
     */
    function setClosedByEmail($value) {
      return $this->setFieldValue('closed_by_email', $value);
    } // setClosedByEmail

    /**
     * Return value of varchar_field_1 field
     *
     * @return string
     */
    function getVarcharField1() {
      return $this->getFieldValue('varchar_field_1');
    } // getVarcharField1
    
    /**
     * Set value of varchar_field_1 field
     *
     * @param string $value
     * @return string
     */
    function setVarcharField1($value) {
      return $this->setFieldValue('varchar_field_1', $value);
    } // setVarcharField1

    /**
     * Return value of varchar_field_2 field
     *
     * @return string
     */
    function getVarcharField2() {
      return $this->getFieldValue('varchar_field_2');
    } // getVarcharField2
    
    /**
     * Set value of varchar_field_2 field
     *
     * @param string $value
     * @return string
     */
    function setVarcharField2($value) {
      return $this->setFieldValue('varchar_field_2', $value);
    } // setVarcharField2

    /**
     * Return value of varchar_field_3 field
     *
     * @return string
     */
    function getVarcharField3() {
      return $this->getFieldValue('varchar_field_3');
    } // getVarcharField3
    
    /**
     * Set value of varchar_field_3 field
     *
     * @param string $value
     * @return string
     */
    function setVarcharField3($value) {
      return $this->setFieldValue('varchar_field_3', $value);
    } // setVarcharField3

    /**
     * Return value of varchar_field_4 field
     *
     * @return string
     */
    function getVarcharField4() {
      return $this->getFieldValue('varchar_field_4');
    } // getVarcharField4
    
    /**
     * Set value of varchar_field_4 field
     *
     * @param string $value
     * @return string
     */
    function setVarcharField4($value) {
      return $this->setFieldValue('varchar_field_4', $value);
    } // setVarcharField4

    /**
     * Return value of integer_field_1 field
     *
     * @return integer
     */
    function getIntegerField1() {
      return $this->getFieldValue('integer_field_1');
    } // getIntegerField1
    
    /**
     * Set value of integer_field_1 field
     *
     * @param integer $value
     * @return integer
     */
    function setIntegerField1($value) {
      return $this->setFieldValue('integer_field_1', $value);
    } // setIntegerField1

    /**
     * Return value of integer_field_2 field
     *
     * @return integer
     */
    function getIntegerField2() {
      return $this->getFieldValue('integer_field_2');
    } // getIntegerField2
    
    /**
     * Set value of integer_field_2 field
     *
     * @param integer $value
     * @return integer
     */
    function setIntegerField2($value) {
      return $this->setFieldValue('integer_field_2', $value);
    } // setIntegerField2

    /**
     * Return value of integer_field_3 field
     *
     * @return integer
     */
    function getIntegerField3() {
      return $this->getFieldValue('integer_field_3');
    } // getIntegerField3
    
    /**
     * Set value of integer_field_3 field
     *
     * @param integer $value
     * @return integer
     */
    function setIntegerField3($value) {
      return $this->setFieldValue('integer_field_3', $value);
    } // setIntegerField3

    /**
     * Return value of date_field_1 field
     *
     * @return DateValue
     */
    function getDateField1() {
      return $this->getFieldValue('date_field_1');
    } // getDateField1
    
    /**
     * Set value of date_field_1 field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setDateField1($value) {
      return $this->setFieldValue('date_field_1', $value);
    } // setDateField1

    /**
     * Return value of date_field_2 field
     *
     * @return DateValue
     */
    function getDateField2() {
      return $this->getFieldValue('date_field_2');
    } // getDateField2
    
    /**
     * Set value of date_field_2 field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setDateField2($value) {
      return $this->setFieldValue('date_field_2', $value);
    } // setDateField2

    /**
     * Return value of date_field_3 field
     *
     * @return DateValue
     */
    function getDateField3() {
      return $this->getFieldValue('date_field_3');
    } // getDateField3
    
    /**
     * Set value of date_field_3 field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setDateField3($value) {
      return $this->setFieldValue('date_field_3', $value);
    } // setDateField3

    /**
     * Return value of datetime_field_1 field
     *
     * @return DateTimeValue
     */
    function getDatetimeField1() {
      return $this->getFieldValue('datetime_field_1');
    } // getDatetimeField1
    
    /**
     * Set value of datetime_field_1 field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setDatetimeField1($value) {
      return $this->setFieldValue('datetime_field_1', $value);
    } // setDatetimeField1

    /**
     * Return value of hash field
     *
     * @return string
     */
    function getHash() {
      return $this->getFieldValue('hash');
    } // getHash
    
    /**
     * Set value of hash field
     *
     * @param string $value
     * @return string
     */
    function setHash($value) {
      return $this->setFieldValue('hash', $value);
    } // setHash

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
          case 'company_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'company_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'company_address':
            return parent::setFieldValue($real_name, (string) $value);
          case 'currency_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'language_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'project_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'subtotal':
            return parent::setFieldValue($real_name, (float) $value);
          case 'tax':
            return parent::setFieldValue($real_name, (float) $value);
          case 'total':
            return parent::setFieldValue($real_name, (float) $value);
          case 'balance_due':
            return parent::setFieldValue($real_name, (float) $value);
          case 'paid_amount':
            return parent::setFieldValue($real_name, (float) $value);
          case 'note':
            return parent::setFieldValue($real_name, (string) $value);
          case 'private_note':
            return parent::setFieldValue($real_name, (string) $value);
          case 'status':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'based_on_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'based_on_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'allow_payments':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'second_tax_is_enabled':
            return parent::setFieldValue($real_name, (boolean) $value);
          case 'second_tax_is_compound':
            return parent::setFieldValue($real_name, (boolean) $value);
          case 'state':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'original_state':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'visibility':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'original_visibility':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'created_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'created_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'created_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'recipient_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'recipient_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'recipient_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'sent_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'sent_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'sent_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'sent_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'reminder_sent_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'closed_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'closed_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'closed_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'closed_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'varchar_field_1':
            return parent::setFieldValue($real_name, (string) $value);
          case 'varchar_field_2':
            return parent::setFieldValue($real_name, (string) $value);
          case 'varchar_field_3':
            return parent::setFieldValue($real_name, (string) $value);
          case 'varchar_field_4':
            return parent::setFieldValue($real_name, (string) $value);
          case 'integer_field_1':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'integer_field_2':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'integer_field_3':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'date_field_1':
            return parent::setFieldValue($real_name, dateval($value));
          case 'date_field_2':
            return parent::setFieldValue($real_name, dateval($value));
          case 'date_field_3':
            return parent::setFieldValue($real_name, dateval($value));
          case 'datetime_field_1':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'hash':
            return parent::setFieldValue($real_name, (string) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }