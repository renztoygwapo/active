<?php

  /**
   * Initialize migration configuration options
   *
   * @package activeCollab.module.system
   * @subpackage migrations
   */
  class MigrateInitMorningPaperOptions extends AngieModelMigration {

    /**
     * Upgrade database
     */
    function up() {
      $this->addConfigOption('morning_paper_enabled', true);
      $this->addConfigOption('morning_paper_include_all_projects', false);
      $this->addConfigOption('morning_paper_last_activity', 0);
    } // up

    /**
     * Downgrade the database
     */
    function down() {
      $this->removeConfigOption('morning_paper_enabled');
      $this->removeConfigOption('morning_paper_include_all_projects');
      $this->removeConfigOption('morning_paper_last_activity');
    } // down
    
  }