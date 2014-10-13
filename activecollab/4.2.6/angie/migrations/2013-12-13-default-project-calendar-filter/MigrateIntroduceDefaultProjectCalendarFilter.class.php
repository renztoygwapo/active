<?php

  /**
   * Introduce default project calendar filter
   *
   * @package angie.migrations
   */
  class MigrateIntroduceDefaultProjectCalendarFilter extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      $this->setConfigOptionValue('default_project_calendar_filter', array(
        'type' => 'everything_in_my_projects'
      ));
    } // up

    /**
     * Migrate down
     */
    function down() {
      $this->removeConfigOption('default_project_calendar_filter');
    } // down

  }