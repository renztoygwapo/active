<?php

  /**
   * Framework level status bar implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  abstract class FwStatusBar extends NamedList {
    
    // Status bar groups
    const GROUP_LEFT = 'left';
    const GROUP_RIGHT = 'right';
    
    /**
     * Indicates whether status bar is loaded or not
     *
     * @var boolean
     */
    protected $is_loaded = false;
    
    /**
     * Returns true if status bar is loaded
     * 
     * @return boolean
     */
    function isLoaded() {
      return $this->is_loaded;
    } // isLoaded
    
    /**
     * Load status bar items
     * 
     * @param IUser $user
     */
    function load(IUser $user) {
      if($this->isLoaded()) {
        return;
      } // if

      if (Trash::canAccess($user)) {
        $this->add('trash', lang('Trash'), Router::assemble('trash'), AngieApplication::getImageUrl('status-bar/trash.png', ENVIRONMENT_FRAMEWORK), array(
          'group' => StatusBar::GROUP_RIGHT,
        ));
      } // if

      EventsManager::trigger('on_status_bar', array(&$this, &$user));
    } // load
    
    // ---------------------------------------------------
    //  Add overrides
    // ---------------------------------------------------
  
    /**
     * Add an item to the row
     * 
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $icon_url
     * @param array $additional
     * @return mixed
     */
    function add($name, $title, $url, $icon_url, $additional = null) {
      $data = array(
        'title' => $title, 
        'url' => $url, 
        'icon' => $icon_url,
      );
      
      if($additional) {
        $data = array_merge($data, $additional);
      } // if
      
      return parent::add($name, $data);
    } // add
    
    /**
     * Add data to the beginning of the list
     *
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $icon_url
     * @param array $additional
     * @return mixed
     */
    function beginWith($name, $title, $url, $icon_url, $additional = null) {
      $data = array(
        'title' => $title, 
        'url' => $url, 
        'icon' => $icon_url,
      );
      
      if($additional) {
        $data = array_merge($data, $additional);
      } // if
      
      return parent::beginWith($name, $data);
    } // beginWith
    
    /**
     * Add data before $before element
     *
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $icon_url
     * @param array $additional
     * @param string $before
     * @return mixed
     */
    function addBefore($name, $title, $url, $icon_url, $additional, $before) {
      $data = array(
        'title' => $title, 
        'url' => $url, 
        'icon' => $icon_url,
      );
      
      if($additional) {
        $data = array_merge($data, $additional);
      } // if
      
      return parent::addBefore($name, $data, $before);
    } // addBefore
    
    /**
     * Add item after $after list element
     *
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $icon_url
     * @param array $additional
     * @param string $after
     * @return mixed
     */
    function addAfter($name, $title, $url, $icon_url, $additional, $after) {
      $data = array(
        'title' => $title, 
        'url' => $url, 
        'icon' => $icon_url,
      );
      
      if($additional) {
        $data = array_merge($data, $additional);
      } // if
      
      return parent::addAfter($name, $data, $after);
    } // addAfter
    
  }