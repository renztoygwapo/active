<?php

  /**
   * Payments framework model definition
   *
   * @package angie.frameworks.payments
   * @subpackage resources
   */
  class PaymentsFrameworkModel extends AngieFrameworkModel {

    /**
     * Construct payments framework model definition
     *
     * @param PaymentsFramework $parent
     */
    function __construct(PaymentsFramework $parent) {
      parent::__construct($parent);

      $this->addModel(DB::createTable('payment_gateways')->addColumns(array(
        DBIdColumn::create(),
        DBTypeColumn::create(),
        DBAdditionalPropertiesColumn::create(),
        DBBoolColumn::create('is_default'),
        DBBoolColumn::create('is_enabled'),
      )))->setTypeFromField('type');

      $this->addModel(DB::createTable('payments')->addColumns(array(
        DBIdColumn::create(),
        DBTypeColumn::create(),
        DBParentColumn::create(),
        DBMoneyColumn::create('amount', 0),
        DBIntegerColumn::create('currency_id', 5),
        DBStringColumn::create('gateway_type', 50),
        DBIntegerColumn::create('gateway_id', 10)->setUnsigned(true),
        DBEnumColumn::create('status', array('Paid', 'Pending', 'Deleted', 'Canceled')),
        DBEnumColumn::create('reason', array('Fraud', 'Refund', 'Other')),
        DBTextColumn::create('reason_text'),
        DBUserColumn::create('created_by'),
        DBDateTimeColumn::create('created_on'),
        DBDateColumn::create('paid_on'),
        DBTextColumn::create('comment'),
        DBStringColumn::create('method', 100),
        DBAdditionalPropertiesColumn::create(),
      ))->addIndices(array(
        DBIndex::create('currency_id'),
        DBIndex::create('status'),
        DBIndex::create('created_on'),
        DBIndex::create('paid_on'),
      )))->setTypeFromField('type');
    } // __construct

    /**
     * Load initial framework data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      $this->addConfigOption('allow_payments', false);
      $this->addConfigOption('allow_payments_for_invoice', false);
      $this->addConfigOption('payment_methods_common', array('Bank Deposit','Check','Cash','Credit','Debit'));
      $this->addConfigOption('payment_methods_credit_card', array('Credit Card','Credit Card (Visa)','Credit Card (Mastercard)','Credit Card (Discover)','Credit Card (American Express)','Credit Card (Diners)'));
      $this->addConfigOption('payment_methods_online', array('Online Payment', 'Online Payment (PayPal)', 'Online Payment (Authorize)'));

      parent::loadInitialData($environment);
    } // loadInitialData

  }