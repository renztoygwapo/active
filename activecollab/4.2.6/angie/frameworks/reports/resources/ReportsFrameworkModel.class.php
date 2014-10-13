<?php

  /**
   * Reports framework model
   *
   * @package angie.frameworks.reports
   * @subpackage resources
   */
  class ReportsFrameworkModel extends AngieFrameworkModel {

    /**
     * Construct environment framework model definition
     *
     * @param ReportsFramework $parent
     */
    function __construct(ReportsFramework $parent) {
      parent::__construct($parent);

      $this->addModel(DB::createTable('data_filters')->addColumns(array(
        DBIdColumn::create(),
        DBTypeColumn::create('DataFilter', 150),
        DBNameColumn::create(50),
        DBAdditionalPropertiesColumn::create(),
        DBDateTimeColumn::create('created_on'),
        DBUserColumn::create('created_by'),
        DBBoolColumn::create('is_private', false),
      ))->addIndices(array(
        DBIndex::create('name'),
      )))->setTypeFromField('type')->getOrderBy('name');
    } // __construct

  }