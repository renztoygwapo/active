<?php

  /**
   * Update activeCollab 3.1.9 to activeCollab 3.1.10
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0041 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.1.9';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.1.10';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'removeSampleTheme' => 'Uninstall Sample Theme (if installed)',
        'addInvoicingConfigOptions' => 'Register new configuration options',
      );
    } // getActions

    /**
     * Remove sample theme from modules table
     *
     * @return bool|string
     */
    function removeSampleTheme() {
      try {
        DB::execute("DELETE FROM " . TABLE_PREFIX . "modules WHERE name IN ('sample_theme')");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // removeSampleTheme

    /**
     * Register new configuration options
     *
     * @return bool|string
     */
    function addInvoicingConfigOptions() {
      try {
        if($this->isModuleInstalled('invoicing')) {
          DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('print_invoices_as', 'invoicing', 'N;')");
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // addInvoicingConfigOptions

  }