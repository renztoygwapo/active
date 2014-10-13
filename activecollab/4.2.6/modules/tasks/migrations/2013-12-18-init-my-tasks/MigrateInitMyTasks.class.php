<?php

  /**
   * Initialize data that is needed for my tasks page
   *
   * @package activeCollab.modules.system
   * @subpackage migrations
   */
  class MigrateInitMyTasks extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      $this->addConfigOption('my_tasks_labels_filter', 'any');
      $this->addConfigOption('my_tasks_labels_filter_data');
    } // up

    /**
     * Migrate down
     */
    function down() {
      $this->removeConfigOption('my_tasks_labels_filter');
      $this->removeConfigOption('my_tasks_labels_filter_data');
    } // down

  }