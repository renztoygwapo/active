<?php

  /**
   * Tools list administration panel row
   * 
   * @package package
   * @subpackage subpackage
   */
  class ToolsAdminPanelRow extends NamedList implements IAdminPanelRow {
    
    /**
     * Row title
     *
     * @var string
     */
    protected $title;
    
    /**
     * Construct admin panel row
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
        $result .= '<li id="admin_panel_tool_' . $k . '"><a href="' . clean($v['url']) . '" title="' . clean($v['title']) . '"><img src="' . clean($v['icon']) . '"><span>' . clean($v['title']) . '</span></a></li>';
        
        if(isset($v['options']) && isset($v['options']['onclick']) && $v['options']['onclick'] instanceof IJavaScriptCallback) {
          $result .= '<script type="text/javascript">$("#admin_panel_tool_' . $k . ' a").each(' . $v['options']['onclick']->render() . ');</script>';
        } // if
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
     * @param array $options
     * @return mixed
     */
    function add($name, $title, $url, $icon_url, $options = null) {
      return parent::add($name, array(
        'title' => $title, 
        'url' => $url, 
        'icon' => $icon_url,
        'options' => $options, 
      ));
    } // add
    
    /**
     * Add data to the beginning of the list
     *
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $icon_url
     * @param array $options
     * @return mixed
     */
    function beginWith($name, $title, $url, $icon_url, $options = null) {
      return parent::beginWith($name, array(
        'title' => $title, 
        'url' => $url, 
        'icon' => $icon_url, 
      	'options' => $options, 
      ));
    } // beginWith
    
    /**
     * Add data before $before element
     *
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $icon_url
     * @param array $options
     * @param string $before
     * @return mixed
     */
    function addBefore($name, $title, $url, $icon_url, $options = null, $before) {
      return parent::addBefore($name, array(
        'title' => $title, 
        'url' => $url, 
        'icon' => $icon_url,
        'options' => $options, 
      ), $before);
    } // addBefore
    
    /**
     * Add item after $after list element
     *
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $icon_url
     * @param array $options
     * @param string $after
     * @return mixed
     */
    function addAfter($name, $title, $url, $icon_url, $options, $after) {
      return parent::addAfter($name, array(
        'title' => $title, 
        'url' => $url, 
        'icon' => $icon_url,
        'options' => $options, 
      ), $after);
    } // addAfter
    
  }