<?php

  /**
   * Assignees framework model definition
   *
   * @package angie.frameworks.assignees
   * @subpackage resources
   */
  class AssigneesFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct assignees frameworks model definition instance
     *
     * @param AssigneesFramework $parent
     */
    function __construct(AssigneesFramework $parent) {
      parent::__construct($parent);
      
      $this->addTable(DB::createTable('assignments')->addColumns(array(
        DBParentColumn::create(false), 
        DBIntegerColumn::create('user_id', 5, 0)->setUnsigned(true), 
      ))->addIndices(array(
        DBIndexPrimary::create(array('parent_type', 'parent_id', 'user_id')), 
        DBIndex::create('user_id', DBIndex::KEY, 'user_id'), 
      )));
    } // __construct
    
    /**
     * Load initial data for a given environment
     * 
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      parent::loadInitialData($environment);
      
      $white = '#FFFFFF';
      $black = '#000000';
      $red = '#FF0000';
      $green = '#00A651';
      $blue = '#0000FF';
      $yellow = '#FFFF00';
      $orange = '#F26522';
      $grey = '#ACACAC';
      
      $labels = array(
        array('NEW', $black, $yellow), 
        array('CONFIRMED', $white, $orange), 
        array('WORKS4ME', $white, $green), 
        array('DUPLICATE', $white, $green), 
        array('WONTFIX', $white, $green), 
        array('ASSIGNED', $white, $red), 
        array('BLOCKED', $black, $grey), 
        array('INPROGRESS', $black, $yellow), 
        array('FIXED', $white, $blue), 
        array('REOPENED', $white, $red), 
        array('VERIFIED', $white, $green), 
      );
      
      $labels_table = TABLE_PREFIX . 'labels';
      
      foreach($labels as $label) {
        list($label_name, $fg_color, $bg_color) = $label;
        
        DB::execute("INSERT INTO $labels_table (type, name, raw_additional_properties) VALUES (?, ?, ?)", 'AssignmentLabel', $label_name, serialize(array('fg_color' => $fg_color, 'bg_color' => $bg_color)));
      } // foreach
    } // loadInitialData
    
  }