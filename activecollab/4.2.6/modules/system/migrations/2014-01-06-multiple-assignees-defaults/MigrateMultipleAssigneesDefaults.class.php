<?php

  /**
   * Set up multiple-assignees configuration option
   *
   * @package activeCollab.modules.system
   * @subpackage migrations
   */
  class MigrateMultipleAssigneesDefaults extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      $this->addConfigOption('multiple_assignees_for_milestones_and_tasks', (boolean) $this->executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'assignments'));
    } // up

    /**
     * Migrate down
     */
    function down() {
      $this->removeConfigOption('multiple_assignees_for_milestones_and_tasks');
    } // down

  }