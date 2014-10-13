<?php

  /**
   * Add index on integer_field_1 because it's often used to find tasks and slows down with bigger data sets
   *
   * @package activeCollab.modules.system
   * @subpackage migrations
   */
  class MigrateAddIndexToIntegerField1 extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      $indexes = DB::listTableIndexes(TABLE_PREFIX . 'project_objects');
      if (!in_array("integer_field_1", $indexes)) {
        DB::execute("ALTER TABLE " . TABLE_PREFIX . "project_objects ADD INDEX integer_field_1 (integer_field_1)");
      } // if
    } // up

    /**
     * Migrate down
     */
    function down() {
      $indexes = DB::listTableIndexes(TABLE_PREFIX . 'project_objects');
      if (in_array("integer_field_1", $indexes)) {
        DB::execute("DROP INDEX integer_field_1 ON " . TABLE_PREFIX . "project_objects");
      } // if
    } // down

  }