<?php

  /**
   * Framework level backend wireframe implementation
   *
   * @package angie.modules.environment
   * @subpackage models
   */
  abstract class FwBackendWireframe extends Wireframe {

    /**
     * Name of the home page route
     *
     * @var string
     */
    protected $homepage_route = 'homepage';
    
    /**
     * Wireframe breadcrumbs
     *
     * @var WireframeBreadcrumbs
     */
    public $breadcrumbs;
    
    /**
     * Wifeframe tabs
     *
     * @var WireframeTabs
     */
    public $tabs;
    
    /**
     * Wireframe actions
     *
     * @var WireframeActions
     */
    public $actions;
    
    /**
     * Wireframe feeds
     *
     * @var WireframeFeeds
     */
    public $feeds;
    
    /**
     * Wireframe print
     *
     * @var WireframePrint
     */
    public $print;

    /**
     * Current menu item
     *
     * @var string
     */
    protected $current_menu_item;
    
    /**
     * Construct backend wireframe
     * 
     * @param Request $request
     * @return FwBackendWireframe
     */
    function __construct(Request $request) {
    	parent::__construct($request);
    	
      if(AngieApplication::getPreferedInterface() == AngieApplication::INTERFACE_PHONE) {
        $this->breadcrumbs = new PhoneWireframeBreadcrumbs();
        $this->actions = new PhoneWireframeActions();
      } else {
        $this->breadcrumbs = new WireframeBreadcrumbs();
        $this->actions = new DefaultWireframeActions();
      } // if
      
      $this->tabs = new WireframeTabs();
      $this->feeds = new WireframeFeeds();
      $this->print = new WireframePrint($this->request);

      if($this->homepage_route) {
        $this->breadcrumbs->add('home', lang('Home'), Router::assemble($this->homepage_route));
      } // if
    } // __construct
    
    /**
     * Return init wireframe parameters
     * 
     * @param User $user
     * @return array
     */
    function getInitParams($user = null) {
      $result = parent::getInitParams($user);
      
      $result['clientside_lang'] = array();
      
      if($user instanceof User) {
        $result['menu_items'] = $this->getMainMenu($user);
        $result['menu_refresh_url'] = Router::assemble('menu_refresh_url');
        $result['statusbar_items'] = $this->getStatusBar($user);
        
        $language = Languages::findByUser($user);
        if ($language instanceof Language) {
        	$result['clientside_lang'] = $language->getTranslation(Language::DICTIONARY_CLIENTSIDE);
        } // if
      } else {
        $result['login_url'] = Router::assemble('login');
        $result['forgot_password_url'] = Router::assemble('forgot_password');
        $result['reset_password_url'] = Router::assemble('reset_password'); 
        $result['login_logo_url'] = AngieApplication::getBrandImageUrl('login-page-logo.png');
        
        $language = Languages::findDefault();
        if ($language instanceof Language) {
        	$result['clientside_lang'] = $language->getTranslation(Language::DICTIONARY_CLIENTSIDE);
        } // if
      } // if
      
      return $result;
    } // getInitParams
    
    // ---------------------------------------------------
    //  Main menu
    // ---------------------------------------------------
    
    /**
     * Return main menu for a given user
     * 
     * @param User $user
     * @return MainMenu
     * @throws InvalidInstanceError
     */
    protected function getMainMenu(User $user) {
      if($user instanceof User) {
        $menu = new MainMenu();
    	  $menu->load($user, false, false);
        
        return $menu;
      } else {
        throw new InvalidInstanceError('user', $user, 'IUser');
      } // if
    } // getMainMenu
    
    /**
     * Return current menu item value
     * 
     * @return string
     */
    function getCurrentMenuItem() {
      return $this->current_menu_item;
    } // getCurrentMenuItem
    
    /**
     * Set current menu item
     * 
     * @param string $value
     */
    function setCurrentMenuItem($value) {
      $this->current_menu_item = $value;
    } // setCurrentMenuItem
    
    // ---------------------------------------------------
    //  Status bar
    // ---------------------------------------------------
    
    /**
     * Return status bar
     * 
     * @param User $user
     * @return StatusBar
     * @throws InvalidInstanceError
     */
    protected function getStatusBar(User $user) {
      if($user instanceof User) {
        $status_bar = new StatusBar();
    	  $status_bar->load($user);
    	  
    	  return $status_bar;
      } else {
        throw new InvalidInstanceError('user', $user, 'IUser');
      } // if
    } // getStatusBar
    
    // ---------------------------------------------------
    //  Asset related settings
    // ---------------------------------------------------
    
    /**
     * Return assets context
     * 
     * @return string
     */
    function getAssetsContext() {
      return 'backend';
    } // getAssetsContext
    
  }