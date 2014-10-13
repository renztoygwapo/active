<?php

	/**
   * Update activeCollab 3.1.3 to activeCollab 3.1.4
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0036 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.1.3';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.1.4';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'fixInvoiceNumber' => 'Fix invoice number column',
        'fixRecurringProfileSpelling' => 'Fix Recurring profile spelling',
      );
    } // getActions

    /**
     * Fix allow_payments flag
     *
     * @return bool|string
     */
    function fixInvoiceNumber() {
      try {
        if($this->isModuleInstalled('invoicing')) {
          DB::execute("ALTER TABLE " . TABLE_PREFIX . "invoices CHANGE number number VARCHAR(50) NULL DEFAULT NULL");
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // fixInvoiceNumber
    
    /**
     * Fix Mounthly to Montly spelling error
     * 
     * @return bool|string
     */
    function fixRecurringProfileSpelling() {
      try {
        if($this->isModuleInstalled('invoicing')) {
          DB::execute("UPDATE " . TABLE_PREFIX . "recurring_profiles SET frequency = 'montly' WHERE frequency = 'mounthly'");
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    }//fixRecurringProfileSpelling

  }