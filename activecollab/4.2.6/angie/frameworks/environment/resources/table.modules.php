<?php

  /**
   * Modules table definition
   *
   * @package angie.frameworks.environment
   * @subpackage resources
   */

  return DB::createTable('modules')->addColumns(array(
    DBNameColumn::create(50),
    DBBoolColumn::create('is_enabled', false),
    DBIntegerColumn::create('position', 6, '0')->setUnsigned(true),
  ))->addIndices(array(
    DBIndexPrimary::create('name'),
  ));