<?php

	/**
   * Update activeCollab 3.1.2 to activeCollab 3.1.3
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0035 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.1.2';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.1.3';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'fixAllowPayments' => 'Fix allow_payments flag',
      );
    } // getActions

    /**
     * Fix allow_payments flag
     *
     * @return bool|string
     */
    function fixAllowPayments() {
      try {
        if($this->isModuleInstalled('invoicing')) {
          DB::execute("ALTER TABLE " . TABLE_PREFIX . "invoices CHANGE allow_payments allow_payments TINYINT(3) NOT NULL DEFAULT '0'");
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // fixAllowPayments

  }