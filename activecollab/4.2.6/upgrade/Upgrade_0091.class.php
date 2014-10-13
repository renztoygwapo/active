<?php

  /**
   * Update activeCollab 4.0.14 to activeCollab 4.0.15
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0091 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '4.0.14';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '4.0.15';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'addUpdatedOnFields' => 'Add UpdatedOn fields',
      );
    } // getActions

    /**
     * Add updatedOn fields
     *
     * @return bool|string
     */
    function addUpdatedOnFields() {
      try {
        $tables_for_upgrade = array(
          'subtasks',
          'expenses',
          'time_records'
        );

        $table_list = DB::listTables(TABLE_PREFIX);

        if(is_foreachable($tables_for_upgrade)) {
          foreach($tables_for_upgrade as $table_for_upgrade) {
            $table_name = TABLE_PREFIX . $table_for_upgrade;

            // check if table exists
            if(in_array($table_name, $table_list)) {
              $table_fields = DB::listTableFields($table_name);

              // check if field exists
              if(!in_array('updated_on', $table_fields)) {
                DB::execute("ALTER TABLE $table_name ADD updated_on DATETIME DEFAULT NULL AFTER created_by_email");
              } // if

              if(!in_array('updated_by_id', $table_fields)) {
                DB::execute("ALTER TABLE $table_name ADD updated_by_id INT(10) UNSIGNED DEFAULT NULL AFTER updated_on");
              } // if

              if(!in_array('updated_by_name', $table_fields)) {
                DB::execute("ALTER TABLE $table_name ADD updated_by_name VARCHAR(100) DEFAULT NULL AFTER updated_by_id");
              } // if

              if(!in_array('updated_by_email', $table_fields)) {
                DB::execute("ALTER TABLE $table_name ADD updated_by_email VARCHAR(150) DEFAULT NULL AFTER updated_by_name");
              } // if
            } // if
          } // foreach
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // if

      return true;
    } // addUpdatedOnFields

  }