<?php

  /**
   * Update activeCollab 3.3.15 to activeCollab 3.3.20
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0079 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.3.15';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.3.20';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateJobTypesTable' => 'Update job types table',
        'addUserJobTypeConfigOption' => 'Add user job type config option'
      );
    } // getActions

    /**
     * Update job types table
     *
     * @return bool|string
     */
    function updateJobTypesTable() {
      if($this->isModuleInstalled('tracking')) {
        try {
          $job_types_table = TABLE_PREFIX . 'job_types';

          if(in_array($job_types_table, DB::listTables(TABLE_PREFIX))) {
            DB::execute("ALTER TABLE $job_types_table ADD is_active TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER is_default");
            DB::execute("UPDATE $job_types_table SET is_active = ?", 1);
          } // if
        } catch(Exception $e) {
          return $e->getMessage();
        } // try
      } // if

      return true;
    } // updateJobTypesTable

    /**
     * Add user job type config option
     *
     * @return bool|string
     */
    function addUserJobTypeConfigOption() {
      try {
        DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('job_type_id', 'system', 'N;')");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // addUserJobTypeConfigOption

  }