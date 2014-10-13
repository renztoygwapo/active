<?php

  /**
   * Update activeCollab 3.3.0 to activeCollab 3.3.1
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0064 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.3.0';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.3.1';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'insertTaskSegmentsTable' => 'Insert Task_segments table',
      );
    } // getActions

    /**
     * Setup auto update
     *
     * @return bool|string
     */
    function insertTaskSegmentsTable() {
      if($this->isModuleInstalled('tasks')) {
        try {
          $task_segments_table = TABLE_PREFIX . 'task_segments';

          if(!in_array($task_segments_table, DB::listTables(TABLE_PREFIX))) {
            $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

            DB::execute("CREATE TABLE $task_segments_table (
              id int(10) unsigned NOT NULL AUTO_INCREMENT,
              name varchar(50) DEFAULT NULL,
              raw_additional_properties longtext,
              PRIMARY KEY (id)
            ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
          } // if
        } catch(Exception $e) {
          return $e->getMessage();
        } // try
      } // if

      return true;
    } // insertTaskSegmentsTable


  }
