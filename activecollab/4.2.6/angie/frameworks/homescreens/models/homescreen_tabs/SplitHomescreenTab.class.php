<?php

  /**
   * Split homescreen tab
   * 
   * @package angie.frameworks.homescreens
   * @subpackage models
   */
  class SplitHomescreenTab extends WidgetsHomescreenTab {
  
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
      2 => HomescreenTab::WIDE_COLUMN,  
    );
    
    /**
     * Return homescreen tab description
     * 
     * @return string
     */
    function getDescription() {
      return lang('Two Columns of the Same Width');
    } // getDescription
    
  }