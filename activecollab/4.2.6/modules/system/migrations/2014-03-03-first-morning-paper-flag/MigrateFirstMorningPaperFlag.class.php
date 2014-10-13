<?php

  /**
   * Set up first morning paper flag
   *
   * @package activeCollab.modules.system
   * @subpackage migrations
   */
  class MigrateFirstMorningPaperFlag extends AngieModelMigration {

    /**
     * Upgrade database
     */
    function up() {
      $this->addConfigOption('first_morning_paper', true);
    } // up

    /**
     * Downgrade the database
     */
    function down() {
      $this->removeConfigOption('first_morning_paper');
    } // down

  }