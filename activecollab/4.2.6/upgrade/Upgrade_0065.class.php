<?php

  /**
   * Update activeCollab 3.3.1 to activeCollab 3.3.2
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0065 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.3.1';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.3.2';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'extendInvoiceItemDescription' => 'Extending invoice item description',
        'fixInvoiceBasedOnRecurringProfile' => 'Fixing Invoices based on Recurring Proffiles',
        'fixSpellingInPaymentMethods' => 'Fixing Spelling in Payment Methods',
        'removeConfigOptions' => 'Update configuration options',
        'addDiskSpaceCachingConfigOptions' => 'Add Disk Space Config Options',
      );
    } // getActions

    /**
     * Extending invoice item description
     *
     * @return bool|string
     */
    function extendInvoiceItemDescription() {
      if($this->isModuleInstalled('invoicing')) {
        try {
          $invoice_object_items_table = TABLE_PREFIX . 'invoice_object_items';
          DB::execute("ALTER TABLE $invoice_object_items_table CHANGE description description TEXT NULL");

          $invoice_item_templates_table = TABLE_PREFIX . 'invoice_item_templates';
          DB::execute("ALTER TABLE $invoice_item_templates_table CHANGE description description TEXT NULL");
        } catch(Exception $e) {
          return $e->getMessage();
        } // try
      } // if

      return true;
    } // extendInvoiceItemDescription

    /**
     * Fixing spelling in payment methods
     *
     * @return bool|string
     */
    function fixSpellingInPaymentMethods() {
      if($this->isModuleInstalled('invoicing')) {
        try {
          $payments_table = TABLE_PREFIX . 'payments';
          DB::execute("UPDATE $payments_table SET method = ? WHERE method = ?", 'Cash', 'Cach');

          $config_table = TABLE_PREFIX . "config_options";
          $methods = DB::executeFirstCell("SELECT value FROM $config_table WHERE name='payment_methods_common'");
          if($methods) {
            $methods = unserialize($methods);
            foreach($methods as $method) {
              $new_method = $method == 'Cach' ?  'Cash' : $method;
              $tmp[] = $new_method;
            } //if
            DB::execute("UPDATE $config_table SET value = ? WHERE name='payment_methods_common'", serialize($tmp));
          } //if
        } catch(Exception $e) {
          return $e->getMessage();
        } // try
      } // if

      return true;
    } // fixSpellingInPaymentMethods

    /**
     * Fixing Invoices based on Recurring Profile
     *
     * @return bool|string
     */
    function fixInvoiceBasedOnRecurringProfile() {
      if($this->isModuleInstalled('invoicing')) {
        try {
          $old_recurring_profiles_table = TABLE_PREFIX . 'backup_recurring_profiles';
          $invoice_objects_table = TABLE_PREFIX . 'invoice_objects';

          if(DB::tableExists($old_recurring_profiles_table)) {
            $old_profiles = DB::execute("SELECT id, name,created_on FROM $old_recurring_profiles_table ", 'RecurringProfile');
            if(is_foreachable($old_profiles)) {
              foreach($old_profiles as $old_profile) {
                $profile_id = DB::executeFirstCell("SELECT id FROM $invoice_objects_table WHERE type=? AND name=? and created_on=?", 'RecurringProfile', $old_profile['name'], $old_profile['created_on']);
                if($profile_id) {
                  DB::execute("UPDATE $invoice_objects_table SET based_on_id = ? WHERE based_on_id = ? AND based_on_type = ? AND type = ?", $profile_id, $old_profile['id'], 'RecurringProfile', 'Invoice');
                }//if
              }//foreach
            }//if
          }//if

        } catch(Exception $e) {
          return $e->getMessage();
        } // try
      } // if

      return true;
    } // fixInvoiceBasedOnRecurringProfile

    /**
     * Remove configuration options that are no longer needed
     *
     * @return bool|string
     */
    function removeConfigOptions() {
      try {
        DB::execute("DELETE FROM " . TABLE_PREFIX . "config_options WHERE name = 'skip_days_off_when_rescheduling'");
      } catch(Exception $e) {
        return $e->getMessage();
      } // if

      return true;
    } // removeConfigOptions

    /**
     * Add Disk Space config options
     *
     * @return bool|string
     */
    function addDiskSpaceCachingConfigOptions() {
      try {
        DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('disk_space_old_versions_size', 'system', 'N;')");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // addDiskSpaceCachingConfigOptions

  }
