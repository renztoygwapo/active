<?php

  /**
   * Update activeCollab 3.3.8 to activeCollab 3.3.9
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0072 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.3.8';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.3.9';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'insertIdSourceUsers' => 'Insert id column in Source Users table',
      );
    } // getActions

    /**
     * Insert id column in Source Users table
     *
     * @return boolean
     */
    function insertIdSourceUsers() {
      if($this->isModuleInstalled('source')) {
        try {
          $source_users_table = TABLE_PREFIX . 'source_users';
          $id_column = DB::execute("SHOW COLUMNS FROM $source_users_table WHERE Field = 'id'");
          if (!$id_column) {
            DB::execute("ALTER TABLE $source_users_table DROP PRIMARY KEY");
            DB::execute("ALTER TABLE $source_users_table ADD id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT FIRST");
          }
        } catch (Exception $e) {
          return $e->getMessage();
        } // try
      } // if

      return true;
    } // insertIdSourceUsers

  } //Upgrade_0072