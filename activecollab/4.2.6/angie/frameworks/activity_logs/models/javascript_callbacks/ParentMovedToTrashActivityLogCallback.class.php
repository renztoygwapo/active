<?php

  /**
   * Parent moved to trash activity log callback
   * 
   * @package angie.frameworks.activity_logs
   * @subpackage models
   */
  class ParentMovedToTrashActivityLogCallback extends ParentActivityLogCallback {
    
    /**
     * Construct callback
     */
    function __construct() {
    	$interface = AngieApplication::getPreferedInterface();

      $this->setRssSubject(lang(':name :type_lowercase moved to trash'));
    	
    	if($interface == AngieApplication::INTERFACE_DEFAULT) {
    		$this->setLang(lang('<a href=":url" title=":name">:name_short</a> :type_lowercase moved to trash'));
    	} elseif($interface == AngieApplication::INTERFACE_PHONE) {
    		$this->setLang(lang(':name :type_lowercase moved to trash'));
    	} elseif($interface == AngieApplication::INTERFACE_PRINTER) {
    		$this->setLang(lang(':name :type_lowercase moved to trash'));
    	} // if
      
			$this->setActionNameLang(lang('Trashed'));
    } // __construct
    
  }