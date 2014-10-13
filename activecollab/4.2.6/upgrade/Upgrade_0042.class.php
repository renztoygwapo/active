<?php

  /**
   * Update activeCollab 3.1.10 to activeCollab 3.1.11
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0042 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.1.10';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.1.11';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
      	'expandingIncomingMailTables' => 'Incoming mail module tables expand',
      	'upgradeProjectRequests' => 'Upgrade project requests',
        'fixNonremovableSubtasks' => 'Fix non-removable subtasks'
      );
    } // getActions
    
    
    /**
     * Add new field in incoming mail filter table
     *
     * @return bool|string
     */
    function expandingIncomingMailTables() {
      try {
        DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'incoming_mail_filters ADD to_email TEXT NULL DEFAULT NULL AFTER sender');
        DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'incoming_mails ADD raw_additional_properties LONGTEXT NULL DEFAULT NULL AFTER created_on');
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // expandingIncomingMailFilter
     
    /**
     * Add new field to project requests
     */
    function upgradeProjectRequests() {
      try {
        DB::execute("ALTER TABLE " . TABLE_PREFIX . "project_requests ADD created_by_company_id INT(10) UNSIGNED DEFAULT NULL AFTER created_by_email");
      } catch (Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // upgradeProjectRequests

    /**
     * Delete subtasks that got stuck with 'Page' parent
     */
    function fixNonremovableSubtasks() {
      try {
        DB::execute("UPDATE " . TABLE_PREFIX . "subtasks SET state = '0' WHERE parent_type = 'Page'");
      } catch (Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // fixNonremovableSubtasks

  }