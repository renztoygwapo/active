<?php

  /**
   * Custom fields framework model definition
   *
   * @package angie.frameworks.custom_fields
   * @subpackage resources
   */
  class CustomFieldsFrameworkModel extends AngieFrameworkModel {

    /**
     * Construct custom fields framework model definition
     *
     * @param CustomFieldsFramework $parent
     */
    function __construct(CustomFieldsFramework $parent) {
      parent::__construct($parent);

      $this->addTable(DB::createTable('custom_fields')->addColumns(array(
        DBStringColumn::create('field_name', 30),
        DBStringColumn::create('parent_type', 50),
        DBStringColumn::create('label', 50),
        DBBoolColumn::create('is_enabled'),
      ))->addIndices(array(
        DBIndex::create('field', DBIndex::PRIMARY, array('field_name', 'parent_type')),
      )));
    } // __construct

  }