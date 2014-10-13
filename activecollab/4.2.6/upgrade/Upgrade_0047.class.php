<?php

  /**
   * Update activeCollab 3.1.15 to activeCollab 3.1.16
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0047 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.1.15';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.1.16';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'addDisableMailboxOptions' => 'Add disable mailbox on successive connection failures options',
      );
    } // getActions
    
    /**
     * Add disable mailbox configuration options
     *
     * @return bool|string
     */
    function addDisableMailboxOptions() {
      try {

        DB::execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, value) VALUES
          ('disable_mailbox_notify_administrator', 'system', 'i:1;'),
          ('disable_mailbox_on_successive_connection_failures', 'system', 'i:1;'),
          ('disable_mailbox_successive_connection_attempts', 'system', 'i:3;');");
        
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // addDisableMailboxOptions

  }