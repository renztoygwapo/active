<?php

  /**
   * Administration panel row interface definition
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  interface IAdminPanelRow {
    
    /**
     * Return row title
     * 
     * @return string
     */
    function getTitle();
    
    /**
     * Return true if this row is not empty (it has content to display)
     * 
     * @return boolean
     */
    function hasContent();
    
    /**
     * Return row content
     * 
     * @return string
     */
    function getContent();
    
  }