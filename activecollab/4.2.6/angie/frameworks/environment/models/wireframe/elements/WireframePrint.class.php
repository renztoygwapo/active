<?php

  /**
   * Wireframe print button implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class WireframePrint implements IWireframeElement {
  	
  	/**
  	 * Current Request 
  	 * 
  	 * @var Request
  	 */
  	protected $request;
  	
  	/**
  	 * Request is necessarry to retrieve right url
  	 * 
  	 * @param Request $request
  	 */
  	function __construct(Request $request) {
  		$this->request = $request;
  	} // __construct
  
    /**
     * Indicator whether print buttons should be displayed or not
     *
     * @var boolean
     */
    private $enabled = false;
    
    /**
     * Enable print
     * 
     * @return boolean
     */
    public function enable() {
    	return $this->enabled = true;
    } // enable
    
    /**
     * Disable print
     * 
     * @return boolean
     */
    public function disable() {
    	return $this->enabled = false;
    } // disable
    
    /**
     * Checks if print is enabled
     *
     * @return boolean
     */
    public function isEnabled() {
    	return $this->enabled;
    } // isEnable
    
    /**
     * Enter description here ...
     * 
     * @return string
     */
    public function getUrl() {
      if($this->isEnabled()) {
        $url_params = array_merge($this->request->getUrlParams(), array('print' => 1));
        
      	if (isset($url_params['inline'])) {
  				unset($url_params['inline']);    		
      	} // if
      	
      	if (isset($url_params['content'])) {
  				unset($url_params['content']);    		
      	} // if
      	
      	if (isset($url_params['async'])) {
  				unset($url_params['async']);    		
      	} // if
      	
      	if (isset($url_params['single'])) {
  				unset($url_params['single']);    		
      	} // if

        if (isset($url_params['quick_view'])) {
          unset($url_params['quick_view']);
        } // if
      	
      	return Router::assemble($this->request->getMatchedRoute(), $url_params);
      } else {
        return false;
      } // if
    } // getUrl
    
    // ---------------------------------------------------
    //  Interface implementation
    // ---------------------------------------------------
    
    /**
     * Event that is triggered when page object is set in wireframe
     * 
     * @param ApplicationObject $object
     * @param IUser $user
     */
    function onPageObjectSet($object, IUser $user) {
      
    } // onPageObjectSet
    
    /**
     * On body classes event handler
     * 
     * @param array $classes
     */
    function onBodyClasses(&$classes) {
    
    } // onBodyClasses
    
  }