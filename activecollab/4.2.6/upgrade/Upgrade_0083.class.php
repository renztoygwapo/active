<?php

  /**
   * Update activeCollab 4.0.5 to activeCollab 4.0.7
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0083 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '4.0.5';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '4.0.7';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'cleanUpConfigOptions' => 'Clean up configuration options',
        'verifyJobTypes' => "Verify Job Types",
      );
    } // getActions

    /**
     * Clean up configuration options
     *
     * @return bool|string
     */
    function cleanUpConfigOptions() {
      try {
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_options WHERE name IN (?)', array('theme', 'license_package' ,'upgrade_to_corporate_url'));
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // cleanUpConfigOptions

    /**
     * Update job types table
     *
     * @return bool|string
     */
    function verifyJobTypes() {
      if($this->isModuleInstalled('tracking')) {
        try {
          $job_types_table = TABLE_PREFIX . "job_types";
          $config_options_table = TABLE_PREFIX . "config_options";

          if(in_array($job_types_table, DB::listTables(TABLE_PREFIX))) {
            $columns = DB::listTableFields($job_types_table);
            if (!in_array("is_active", $columns)) {
              DB::execute("ALTER TABLE $job_types_table ADD is_active TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER is_default");
              DB::execute("UPDATE $job_types_table SET is_active = ?", 1);
            } // if
          } // if

          $job_type_id_exists = DB::executeFirstCell("SELECT COUNT(*) FROM $config_options_table WHERE name = 'job_type_id'") > 0;
          if (!$job_type_id_exists) {
            DB::execute("INSERT INTO $config_options_table  (name, module, value) VALUES ('job_type_id', 'system', 'N;')");
          } // if

        } catch(Exception $e) {
          return $e->getMessage();
        } // try
      } // if

      return true;
    } // verifyJobTypes

  }