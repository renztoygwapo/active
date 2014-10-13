<?php

  /**
   * Initialize performance checklist
   *
   * @package angie.migrations
   */
  class MigrateInitializePerformanceChecklist extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      $this->addConfigOption('control_tower_check_performance', true);
    } // up

    /**
     * Migrate down
     */
    function down() {
      $this->removeConfigOption('control_tower_check_performance');
    } // down

  }