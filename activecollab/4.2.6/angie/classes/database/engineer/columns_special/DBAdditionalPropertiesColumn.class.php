<?php

  /**
   * Additional properties column implementation
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBAdditionalPropertiesColumn extends DBTextColumn {
    
    /**
     * Construct additional properties column
     */
    function __construct() {
      parent::__construct('raw_additional_properties');
      $this->setSize(self::BIG);
    } // __construct
    
    /**
     * Create and return new additional properties column
     *
     * @return DBAdditionalPropertiesColumn
     */
    static function create() {
      return new DBAdditionalPropertiesColumn();
    } // create
    
  }