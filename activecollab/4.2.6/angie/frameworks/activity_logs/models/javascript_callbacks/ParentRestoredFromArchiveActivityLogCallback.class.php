<?php

  /**
   * Parent restored from archive activity log callback
   * 
   * @package angie.frameworks.activity_logs
   * @subpackage models
   */
  class ParentRestoredFromArchiveActivityLogCallback extends ParentActivityLogCallback {
    
    /**
     * Construct callback
     */
    function __construct() {
    	$interface = AngieApplication::getPreferedInterface();

      $this->setRssSubject(lang(':name :type_lowercase restored from archive'));
    	
    	if($interface == AngieApplication::INTERFACE_DEFAULT) {
    		$this->setLang(lang('<a href=":url" title=":name">:name_short</a> :type_lowercase restored from archive'));
    	} elseif($interface == AngieApplication::INTERFACE_PHONE) {
    		$this->setLang(lang(':name :type_lowercase restored from archive'));
    	} // if

			$this->setActionNameLang(lang('Unarchived'));
    } // __construct
    
  }