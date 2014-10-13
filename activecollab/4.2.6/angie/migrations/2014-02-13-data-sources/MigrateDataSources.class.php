<?php

  /**
   * Introduce alternative user addresses
   *
   * @package angie.migrations
   */
  class MigrateDataSources extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      $data_sources_table = TABLE_PREFIX . 'data_sources';
      if(!DB::tableExists($data_sources_table)) {
        $this->createTable('data_sources', array(
          DBIdColumn::create(),
          DBTypeColumn::create('ApplicationObject'),
          DBNameColumn::create(50),
          DBAdditionalPropertiesColumn::create(),
          DBIntegerColumn::create('created_by_id', 10),
          DBStringColumn::create('created_by_name', 100),
          DBStringColumn::create('created_by_email', 100),
          DBBoolColumn::create('is_private', false),
          DBDateTimeColumn::create('created_on'),
        ));
      } //if

      $data_sources_mappings_table = TABLE_PREFIX . 'data_source_mappings';
      if(!DB::tableExists($data_sources_mappings_table)) {
        $this->createTable('data_source_mappings', array(
          DBIdColumn::create(),
          DBIntegerColumn::create('project_id', 11),
          DBStringColumn::create('source_type', 50, ''),
          DBIntegerColumn::create('source_id', 11),
          DBIntegerColumn::create('parent_id', 11),
          DBStringColumn::create('parent_type', 50, ''),
          DBIntegerColumn::create('external_id', 11),
          DBStringColumn::create('external_type', 50, ''),
          DBDateTimeColumn::create('created_on'),
          DBIntegerColumn::create('created_by_id', 10)->setUnsigned(true),
          DBStringColumn::create('created_by_name', 100),
          DBStringColumn::create('created_by_email', 150),
        ));
      } //if
    } // up

    /**
     * Migrate down
     */
    function down() {
      $this->dropTable('data_sources');
      $this->dropTable('data_source_mappings');
    } // down

  }