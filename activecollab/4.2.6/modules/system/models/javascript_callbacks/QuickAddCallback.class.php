<?php

  /**
   * Quick Add callback
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class QuickAddCallback extends JavaScriptCallback {
  	
  	const GROUP_PROJECT = 'project';
  	const GROUP_MAIN = 'main';
  	const GROUP_OTHER = 'other';
    
    /**
     * Additional options
     *
     * @var Object
     */
    protected $options;
    
    /**
     * User for which is this callback created
     * 
     * @var User
     */
    protected $user;
    
    /**
     * construct
     *
     * @param array $options
     */
    function __construct($options = null) {
      $this->options = $options;      
    } // __construct
        
    /**
     * Render callback body
     *
     * @return string
     */
    function render() {
      $cached_data = AngieApplication::cache()->getByObject(Authentication::getLoggedUser(), array('quick_add', AngieApplication::INTERFACE_DEFAULT));
      
    	if ($cached_data) {
    		$this->options['data'] = $cached_data;
    	} // if
    	
      return '(function () { $(this).quickAdd(' . JSON::encode($this->options) . '); })';
    } // render
    
  }