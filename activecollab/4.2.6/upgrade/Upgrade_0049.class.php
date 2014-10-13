<?php

  /**
   * Update activeCollab 3.1.17 to activeCollab 3.2
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0049 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.1.17';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.2.0';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'initCustomFields' => 'Initialize custom fields for projects and tasks',
        'initRelatedTasks' => 'Initialize related tasks',
        'updateTaxRateTable'	=> 'Updating tax rate table',
      );
    } // getActions

    
    /**
     * Upgrade tax rate table
     *
     * @return bool|string
     */
    function updateTaxRateTable() {
      try {
        DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'tax_rates ADD is_default TINYINT(1) NULL DEFAULT NULL AFTER percentage');
      } catch(Exception $e) {
        return $e->getMessage();
      } // if
      return true;
    } // updateTaxRateTable
    
    
    /**
     * Initialize custom fields
     *
     * @return bool|string
     */
    function initCustomFields() {
      try {
        DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'projects ADD custom_field_1 VARCHAR(255) NULL DEFAULT NULL AFTER updated_by_email');
        DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'projects ADD custom_field_2 VARCHAR(255) NULL DEFAULT NULL AFTER custom_field_1');
        DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'projects ADD custom_field_3 VARCHAR(255) NULL DEFAULT NULL AFTER custom_field_2');

        DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'project_objects ADD custom_field_1 VARCHAR(255) NULL DEFAULT NULL AFTER boolean_field_3');
        DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'project_objects ADD custom_field_2 VARCHAR(255) NULL DEFAULT NULL AFTER custom_field_1');
        DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'project_objects ADD custom_field_3 VARCHAR(255) NULL DEFAULT NULL AFTER custom_field_2');

        $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';
        $custom_fields_table = TABLE_PREFIX . 'custom_fields';

        DB::execute("CREATE TABLE $custom_fields_table (
          field_name varchar(30) DEFAULT NULL,
          parent_type varchar(50) DEFAULT NULL,
          label varchar(50) DEFAULT NULL,
          is_enabled tinyint(1) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (field_name, parent_type)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

        DB::execute("INSERT INTO $custom_fields_table (field_name, parent_type, label, is_enabled) VALUES ('custom_field_1', 'Project', NULL, 0), ('custom_field_2', 'Project', NULL, 0), ('custom_field_3', 'Project', NULL, 0)");

        if($this->isModuleInstalled('tasks')) {
          DB::execute("INSERT INTO $custom_fields_table (field_name, parent_type, label, is_enabled) VALUES ('custom_field_1', 'Task', NULL, 0), ('custom_field_2', 'Task', NULL, 0), ('custom_field_3', 'Task', NULL, 0)");
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // if

      return true;
    } // initCustomFields

    /**
     * Initialize related tasks table
     *
     * @return bool|string
     */
    function initRelatedTasks() {
      try {
        if($this->isModuleInstalled('tasks')) {
          $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

          DB::execute("CREATE TABLE " . TABLE_PREFIX . "related_tasks (
            parent_task_id int(10) unsigned NOT NULL DEFAULT 0,
            related_task_id int(10) unsigned NOT NULL DEFAULT 0,
            note varchar(255) DEFAULT NULL,
            created_on datetime DEFAULT NULL,
            created_by_id int unsigned NULL DEFAULT NULL,
            created_by_name varchar(100) DEFAULT NULL,
            created_by_email varchar(150) DEFAULT NULL,
            INDEX created_on (created_on),
            PRIMARY KEY (parent_task_id, related_task_id)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        } // if
      } catch(Exception $e) {
        $e->getMessage();
      } // try

      return true;
    } // initRelatedTasks

  }