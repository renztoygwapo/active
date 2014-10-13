<?php

  /**
   * Add is_mentioned migration column
   *
   * @package angie.migrations
   */
  class MigrateAddIsMentionedColumn extends AngieModelMigration {

    /**
     * Up the database
     */
    function up() {
      $this->loadTable('notification_recipients')->addColumn(DBBoolColumn::create('is_mentioned', false));
    } // up

    /**
     * Down the database
     */
    function down() {
      $this->loadTable('notification_recipients')->dropColumn('is_mentioned');
    } // down

  }