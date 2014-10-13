<?php

  /**
   * Update activeCollab 3.2.6 to activeCollab 3.2.7
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0055 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.2.6';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.2.7';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateInvoiceNotificationOptions' => 'Upgrade invoice notification options',
      );
    } // getActions

    /**
     * Upgrade invoicing notifications
     *
     * @return bool|string
     */
    function updateInvoiceNotificationOptions() {
      if ($this->isModuleInstalled('invoicing')) {
        try {
          DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('invoice_notify_on_payment', 'invoicing', 'b:1;')"); 
          DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('invoice_notify_on_cancel', 'invoicing', 'b:1;')");
          DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('invoice_notify_financial_manager_ids', 'invoicing', 'N;')"); 
          DB::execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, value) VALUES ('invoice_notify_financial_managers', 'invoicing', ?)", serialize('Notify All Financial Managers'));
          
        } catch(Exception $e) {
          return $e->getMessage();
        } // try
      } //if
      return true;
    } // updateInvoiceNotificationOptions

  }