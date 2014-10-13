<?php

  /**
   * Homescreen tab where main column is in center
   * 
   * @package angie.frameworks.homescreens
   * @subpackage models
   */
  class CenterHomescreenTab extends WidgetsHomescreenTab {
  
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
      1 => HomescreenTab::NARROW_COLUMN, 
      2 => HomescreenTab::WIDE_COLUMN, 
      3 => HomescreenTab::NARROW_COLUMN, 
    );
    
    /**
     * Return homescreen tab description
     * 
     * @return string
     */
    function getDescription() {
      return lang('Three Columns, Center Column is Wide');
    } // getDescription
    
  }