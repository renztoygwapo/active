<?php

  /**
   * Framework level main menu implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage model
   */
  class FwMainMenu extends NamedList {
    
    /**
     * Indicates whether status bar is loaded or not
     *
     * @var boolean
     */
    protected $is_loaded = false;

    /**
     * Array of restrictions we want to extract
     *
     * @var bool
     */
    protected $restrictions = false;

    /**
     * Detailed
     *
     * @var bool
     */
    protected $detailed = false;
    
    /**
     * Returns true if status bar is loaded
     * 
     * @return boolean
     */
    function isLoaded() {
      return $this->is_loaded;
    } // isLoaded

    /**
     * Is item allowed
     *
     * @param String $item
     * @return booelan
     */
    function isAllowed($item) {
      return !is_foreachable($this->restrictions) || in_array($item, $this->restrictions);
    } // isAllowed

    /**
     * is detailed loading on
     *
     * @return boolean
     */
    function isDetailed() {
      return $this->detailed;
    } // isDetailed

    /**
     * Load status bar items
     * 
     * @param User $user
     * @param boolean $detailed
     * @param mixed $item_restrictions
     */
    function load(User $user, $detailed = false, $item_restrictions = false) {
      if($this->isLoaded()) {
        return;
      } // if

      $this->restrictions = $item_restrictions;
      $this->detailed = $detailed;

      if ($this->isAllowed('homepage')) {
        $this->add('homepage', lang('Home Screen'), Router::assemble('homepage'), AngieApplication::getImageUrl('main-menu/homepage.png', ENVIRONMENT_FRAMEWORK));
      } // if

      
      if($this->isAllowed('admin') && $user->isAdministrator()) {
        $control_tower = new ControlTower($user);

        $admin_item = array(
          'badge' => $control_tower->loadBadgeValue(),
          'popup' => array(
            'header'          => array('title' => lang('Administration')),
            'handler'         => 'admin',
            'auto_refresh'    => true,
            'additional'      => array(
              'administration_url'  => Router::assemble('admin')
            )
          )
        );

        if ($this->isDetailed()) {
          $control_tower->load();
          $admin_item['popup']['additional']['control_tower'] = $control_tower->render();
        } // if

  	    $this->add('admin', lang('Administration'), Router::assemble('admin'), AngieApplication::getImageUrl('main-menu/administration.png', ENVIRONMENT_FRAMEWORK), $admin_item);
  	  } // if

      if ($this->isAllowed('profile')) {
        $profile_item = array(
          'group' => 'profile',
          'popup' => array(
            'header'          => array('title' => $user->getDisplayName()),
            'handler'         => 'profile_menu',
            'auto_refresh'    => true,
            'additional'      => array(
              'user'                  => $user->describe($user, true, true),
              'change_settings_url'   => $user->getEditSettingsUrl(),
              'update_profile_url'    => $user->getEditProfileUrl(),
              'change_password_url'   => $user->getEditPasswordUrl(),
              'logout_url'            => Router::assemble('logout')
            )
          )
        );

        if ($this->isDetailed()) {
          $profile_item['popup']['additional']['favorites'] = JSON::valueToMap(Favorites::findFavoriteObjectsByUser($user));
        } // if

        $this->add('profile', $user->getDisplayName(), $user->getViewUrl(), $user->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG), $profile_item);
      } // if

      EventsManager::trigger('on_main_menu', array(&$this, &$user));
    } // load
    
    // ---------------------------------------------------
    //  Overrides
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