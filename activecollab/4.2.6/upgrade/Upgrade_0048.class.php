<?php

  /**
   * Update activeCollab 3.1.16 to activeCollab 3.1.17
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0048 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.1.16';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.1.17';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateUsersTable' => 'Update users table',
        'addInvoicingConfigOptions' => 'Add new invoice module options',
        'addControlTowerConfigOptions' => 'Add new control tower options',
      );
    } // getActions

    /**
     * Update users table
     *
     * @return bool|string
     */
    function updateUsersTable() {
      try {
        $users_table = TABLE_PREFIX . 'users';

        DB::execute("ALTER TABLE $users_table CHANGE password password VARCHAR(255) NOT NULL  DEFAULT ''");
        DB::execute("ALTER TABLE $users_table ADD password_hashed_with ENUM('pbkdf2', 'sha1') NOT NULL DEFAULT 'pbkdf2' AFTER password");
        DB::execute("ALTER TABLE $users_table ADD password_expires_on DATE NULL DEFAULT NULL AFTER password_hashed_with");

        DB::execute("UPDATE $users_table SET password_hashed_with = 'sha1'");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateUsersTable

    /**
     * Register new configuration options
     *
     * @return bool|string
     */
    function addInvoicingConfigOptions() {
      try {
        if($this->isModuleInstalled('invoicing')) {
          DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('print_proforma_invoices_as', 'invoicing', 'N;')");
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // addInvoicingConfigOptions

    /**
     * Register new configuration options for control tower
     *
     * @return bool|string
     */
    function addControlTowerConfigOptions() {
      try {
        DB::execute('REPLACE INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('control_tower_check_for_new_version', 'system', 'b:1;');");
      } catch(Exception $e) {
        return $e->getMessage();
      } // if

      return true;
    } // addControlTowerConfigOptions

  }