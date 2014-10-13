<?php

  /**
   * User addresses table definition
   *
   * @package angie.frameworks.authentication
   * @subpackage resources
   */

  return DB::createTable('user_addresses')->addColumns(array(
    DBIntegerColumn::create('user_id', 10, '0')->setUnsigned(true),
    DBStringColumn::create('email', 150, ''),
  ))->addIndices(array(
    DBIndexPrimary::create(array('user_id', 'email')),
  ));