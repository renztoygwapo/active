<?php

  /**
   * Homescreen tab where main column is on the left
   * 
   * @package angie.frameworks.homescreens
   * @subpackage models
   */
  class LeftHomescreenTab extends WidgetsHomescreenTab {
  
    /**
     * This home screen tab does accepts widgets
     *
     * @var boolean
     */
    protected $accept_widgets = true;
    
    /**
     * Column definitions (none)
     *
     * @var array
     */
    protected $columns = array(
      1 => HomescreenTab::WIDE_COLUMN, 
      2 => HomescreenTab::NARROW_COLUMN, 
      3 => HomescreenTab::NARROW_COLUMN, 
    );
    
    /**
     * Return homescreen tab description
     * 
     * @return string
     */
    function getDescription() {
      return lang('Three Columns, Left Column is Wide');
    } // getDescription
    
  }