<?php

  /**
   * Update activeCollab 3.1.12 to activeCollab 3.1.13
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0044 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.1.12';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.1.13';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateConflictNotificationSettings' => 'Update conflict notification settings',
        'updateRecurringProfileTable'	=> 'Update recurring profile table',
        'updateVersionCheckingOptions' => 'Update version checking options',
        'updateTrackingOptions' => 'Update tracking module options',
      );
    } // getActions

    /**
     * Update conflict notification settings
     *
     * @return bool
     */
    function updateConflictNotificationSettings() {
      try {
        $config_options_table = TABLE_PREFIX . 'config_options';

        if(DB::executeFirstCell("SELECT COUNT(name) FROM $config_options_table WHERE name = 'conflict_notifications_delivery'") < 1) {
          DB::execute("INSERT INTO $config_options_table (name, module, value) VALUES ('conflict_notifications_delivery', 'system', 'i:2;')");
        } // if
      } catch (Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateConflictNotificationSettings

    /**
     * Add 'invoice_due_after' to recurring profiles table
     * 
     * @return boolean
     */
    function updateRecurringProfileTable() {
      try {
        if($this->isModuleInstalled('invoicing')) {
          DB::execute("ALTER TABLE " . TABLE_PREFIX . "recurring_profiles ADD invoice_due_after INT(5) UNSIGNED DEFAULT 0 AFTER auto_issue");
          DB::execute("ALTER TABLE " . TABLE_PREFIX . "recurring_profile_items CHANGE quantity quantity DECIMAL(12,2) NOT NULL DEFAULT '0'");
        } // if
      } catch (Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    }//updateRecurringProfileTable
    
    /**
     * Update version checking configuration options
     *
     * @return bool|string
     */
    function updateVersionCheckingOptions() {
      try {
        DB::execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, value) VALUES ('update_instructions_url', 'system', ?)", serialize('http://www.activecollab.com/user/' . LICENSE_UID . '/profile'));
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateVersionCheckingOptions

    /**
     * Return tracking options
     *
     * @return mixed
     */
    function updateTrackingOptions() {
      $config_options_table = TABLE_PREFIX . 'config_options';

      try {
        DB::beginWork('Updating tracking module options @ ' . __CLASS__);

        DB::executeFirstColumn("DELETE FROM $config_options_table WHERE name = 'auto_update_enabled'"); // Added by mistake

        if($this->isModuleInstalled('tracking')) {
          DB::executeFirstColumn("INSERT INTO $config_options_table (name, module, value) VALUES ('default_billable_status', 'tracking', 'i:1;')");
        } // if

        DB::commit('Tracking module options updated @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to update tracking module options @ ' . __CLASS__);

        return $e->getMessage();
      } // if

      return true;
    } // updateTrackingOptions

  }