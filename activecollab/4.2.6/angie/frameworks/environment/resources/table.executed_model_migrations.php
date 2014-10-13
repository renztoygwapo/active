<?php

  /**
   * Executed model migrations
   *
   * @package angie.frameworks.enviornment
   * @subpackage resources
   */

  return DB::createTable('executed_model_migrations')->addColumns(array(
    DBIdColumn::create(DBColumn::SMALL),
    DBStringColumn::create('migration', 255, ''),
    DBDateColumn::create('changeset_timestamp'),
    DBStringColumn::create('changeset_name', 255),
    DBDateTimeColumn::create('executed_on'),
  ))->addIndices(array(
    DBIndex::create('migration', DBIndex::UNIQUE, 'migration'),
    DBIndex::create('executed_on'),
  ));