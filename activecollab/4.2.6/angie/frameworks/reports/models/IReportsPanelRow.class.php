<?php

  /**
   * Reports panel row interface definition
   * 
   * @package angie.frameworks.reports
   * @subpackage models
   */
  interface IReportsPanelRow {
    
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