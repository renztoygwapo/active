<?php

  /**
   * Base reports panel
   * 
   * @package angie.frameworks.reports
   * @subpackage models
   */
  abstract class FwReportsPanel implements IteratorAggregate {
  
    /**
     * Array of panel rows
     *
     * @var NamedList
     */
    protected $rows;
    
    /**
     * Construct reports panel instance
     */
    function __construct() {
      $this->rows = new NamedList(array(
        'general' => new ReportsPanelRow(lang('General')),
      ));
    } // __construct
    
    /**
     * Add a tool to the list of general reports
     * 
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $icon_url
     * @param mixed $options
     */
    function addToGeneral($name, $title, $url, $icon_url, $options = null) {
      $this->addTo('general', $name, $title, $url, $icon_url, $options);
    } // addToGeneral
    
    /**
     * Add a tool to the specified row
     * 
     * @param string $row_name
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $icon_url
     * @param mixed $options
     * @throws InvalidParamError
     */
    function addTo($row_name, $name, $title, $url, $icon_url, $options = null) {
      if($this->row($row_name) instanceof ReportsPanelRow) {
        if($options && isset($options['after'])) {
          $this->row($row_name)->addAfter($name, $title, $url, $icon_url, $options['after']);
        } elseif($options && isset($options['before'])) {
          $this->row($row_name)->addBefore($name, $title, $url, $icon_url, $options['before']);
        } elseif($options && isset($options['begin_with']) && $options['begin_with']) {
          $this->row($row_name)->beginWith($name, $title, $url, $icon_url);
        } else {
          $this->row($row_name)->add($name, $title, $url, $icon_url);
        } // if
      } else {
        throw new InvalidParamError('row_name', $row_name);
      } // if
    } // addTo
    
    // ---------------------------------------------------
    //  Rows
    // ---------------------------------------------------
    
    /**
     * Return row with a given name
     * 
     * @param string $name
     * @return ReportsPanelRow
     * @throws InvalidParamError
     */
    protected function row($name) {
      if($this->rows->exists($name)) {
        return $this->rows->get($name);
      } else {
        throw new InvalidParamError('name', $name, "Row '$name' is not defined");
      } // if
    } // row

    /**
     * Returns true if $name row exists
     *
     * @param string $name
     * @return boolean
     */
    function rowExists($name) {
      return $this->rows->get($name);
    } // rowExists
    
    /**
     * Define reports panel row
     * 
     * @param string $name
     * @param IReportsPanelRow $row
     */
    function defineRow($name, IReportsPanelRow $row, $options = null) {
      if($options && isset($options['after'])) {
        $this->rows->addAfter($name, $row, $options['after']);
      } elseif($options && isset($options['before'])) {
        $this->rows->addBefore($name, $row, $options['before']);
      } elseif($options && isset($options['begin_with']) && $options['begin_with']) {
        $this->rows->beginWith($name, $row);
      } else {
        $this->rows->add($name, $row);
      } // if
    } // defineRow
    
    // ---------------------------------------------------
    //  Interface implementation
    // ---------------------------------------------------
    
    /** 
     * Returns an iterator for for this object, for use with foreach 
     * 
     * @return ArrayIterator 
     */ 
     function getIterator() { 
       return $this->rows->getIterator(); 
     } // getIterator
    
  }