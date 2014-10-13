<?php

  /**
   * Based administration panel definition
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  abstract class FwAdminPanel implements IteratorAggregate {
  
    /**
     * Array of panel rows
     *
     * @var NamedList
     */
    protected $rows;

    /**
     * User for which is this panel rendered
     *
     * @var User
     */
    protected $user;
    
    /**
     * Construct administration panel instance
     *
     * @param User $user
     */
    function __construct(User $user) {
      $this->user = $user;

      $this->rows = new NamedList(array(
        'general' => new ToolsAdminPanelRow(lang('General')), 
      	'tools' => new ToolsAdminPanelRow(lang('Tools')),
      ));

      $system_info_row = $this->getSystemInformationRow();

      if($system_info_row instanceof IAdminPanelRow) {
        $this->rows->add('system', $system_info_row);
      } // if
    } // __construct
    
    /**
     * Add a tool to the list of general tools
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
     * Add a tool to the list of tools
     * 
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $icon_url
     * @param mixed $options
     */
    function addToTools($name, $title, $url, $icon_url, $options = null) {
      $this->addTo('tools', $name, $title, $url, $icon_url, $options);
    } // addToTools
    
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
    protected function addTo($row_name, $name, $title, $url, $icon_url, $options = null) {
      if($this->row($row_name) instanceof ToolsAdminPanelRow) {
        $after = array_var($options, 'after');
        $before = array_var($options, 'before');
        $begin_with = array_var($options, 'begin_with');
        
        if($after) {
          $this->row($row_name)->addAfter($name, $title, $url, $icon_url, $options, $after);
        } elseif($before) {
          $this->row($row_name)->addBefore($name, $title, $url, $icon_url, $options, $before);
        } elseif($begin_with) {
          $this->row($row_name)->beginWith($name, $title, $url, $icon_url, $options);
        } else {
          $this->row($row_name)->add($name, $title, $url, $icon_url, $options);
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
     * @return ToolsAdminPanelRow
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
     * Define administration panel row
     * 
     * @param string $name
     * @param IAdminPanelRow $row
     */
    protected function defineRow($name, IAdminPanelRow $row, $options = null) {
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

    /**
     * Return system information row
     *
     * @return IAdminPanelRow
     */
    protected function getSystemInformationRow() {
      return null;
    } // getSystemInformationRow
    
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