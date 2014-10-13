<?php

  /**
   * Reports panel row
   * 
   * @package angie.frameworks.reports
   * @subpackage models
   */
  class ReportsPanelRow extends NamedList implements IReportsPanelRow {
  
    /**
     * Row title
     *
     * @var string
     */
    protected $title;
    
    /**
     * Construct reports panel row
     * 
     * @param string $title
     * @param mixed $data
     */
    function __construct($title, $data = null) {
      parent::__construct($data);
      
      $this->title = $title;
    } // __construct
    
    // ---------------------------------------------------
    //  Interface Implementation
    // ---------------------------------------------------
    
    /**
     * Return row title
     * 
     * @return string
     */
    function getTitle() {
      return $this->title;
    } // getTitle
    
    /**
     * Return true if this row is not empty (it has content to display)
     * 
     * @return boolean
     */
    function hasContent() {
      return $this->count() > 0;
    } // hasContent
    
    /**
     * Return row content
     * 
     * @return string
     */
    function getContent() {
      $result = '<ul class="tools">';
      
      foreach($this as $k => $v) {
        $result .= '<li><a href="' . clean($v['url']) . '"><img src="' . clean($v['icon']) . '"><span>' . clean($v['title']) . '</span></a></li>';
      } // if
      
      return "$result</ul>";
    } // getContent
    
    // ---------------------------------------------------
    //  Data Functions
    // ---------------------------------------------------
    
    /**
     * Add an item to the row
     * 
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $icon_url
     * @return mixed
     */
    function add($name, $title, $url, $icon_url) {
      return parent::add($name, array(
        'title' => $title, 
        'url' => $url, 
        'icon' => $icon_url,
      ));
    } // add
    
    /**
     * Add data to the beginning of the list
     *
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $icon_url
     * @return mixed
     */
    function beginWith($name, $title, $url, $icon_url) {
      return parent::beginWith($name, array(
        'title' => $title, 
        'url' => $url, 
        'icon' => $icon_url,
      ));
    } // beginWith
    
    /**
     * Add data before $before element
     *
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $icon_url
     * @param string $before
     * @return mixed
     */
    function addBefore($name, $title, $url, $icon_url, $before) {
      return parent::addBefore($name, array(
        'title' => $title, 
        'url' => $url, 
        'icon' => $icon_url,
      ), $before);
    } // addBefore
    
    /**
     * Add item after $after list element
     *
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $icon_url
     * @param string $after
     * @return mixed
     */
    function addAfter($name, $title, $url, $icon_url, $after) {
      return parent::addAfter($name, array(
        'title' => $title, 
        'url' => $url, 
        'icon' => $icon_url,
      ), $after);
    } // addAfter
    
  }