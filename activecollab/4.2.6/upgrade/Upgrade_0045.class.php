<?php

  /**
   * Update activeCollab 3.1.13 to activeCollab 3.1.14
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0045 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.1.13';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.1.14';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'initControlTower' => 'Initialize control tower settings',
      );
    } // getActions

//    /**
//     * Clean up binary data of files that are permanently deleted
//     */
//    function cleanUpDeletedFiles() {
//      try {
//        // @TODO
//      } catch(Exception $e) {
//        return $e->getMessage();
//      } // if
//
//      return true;
//    } // cleanUpDeletedFiles

    /**
     * Initialize control tower settings
     *
     * @return bool|string
     */
    function initControlTower() {
      try {
        DB::execute('REPLACE INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES
          ('control_tower_check_scheduled_tasks', 'system', 'b:1;'),
          ('control_tower_check_disk_usage', 'system', 'b:1;'),
          ('control_tower_check_reply_to_comment', 'system', 'b:1;'),
          ('control_tower_check_email_queue', 'system', 'b:1;'),
          ('control_tower_check_email_conflicts', 'system', 'b:1;');");
      } catch(Exception $e) {
        return $e->getMessage();
      } // if

      return true;
    } // initControlTower

  }