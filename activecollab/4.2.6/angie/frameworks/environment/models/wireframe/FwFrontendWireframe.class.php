<?php

  /**
   * Framework level frontend wireframe implementation
   *
   * @package angie.modules.environment
   * @subpackage models
   */
  abstract class FwFrontendWireframe extends Wireframe {
    
    /**
     * Wireframe breadcrumbs
     *
     * @var WireframeBreadcrumbs
     */
    public $breadcrumbs;
    
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
     * Construct backend wireframe
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
    } // __construct
    
    /**
     * Return init wireframe parameters
     * 
     * @param User $user
     * @return array
     */
    function getInitParams($user = null) {
      $result = parent::getInitParams($user);

      $language = Languages::findDefault();
      if ($language instanceof Language) {
      	$result['clientside_lang'] = $language->getTranslation(Language::DICTIONARY_CLIENTSIDE);
      } else {
      	$result['clientside_lang'] = array();	
      } // if
      
      return $result;
    } // getInitParams
    
    /**
     * Return assets context
     * 
     * @return string
     */
    function getAssetsContext() {
      return 'frontend';
    } // getAssetsContext
    
  }