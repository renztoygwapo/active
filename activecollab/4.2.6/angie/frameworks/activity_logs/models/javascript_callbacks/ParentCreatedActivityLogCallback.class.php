<?php

  /**
   * Parent created activity log callback
   * 
   * @package angie.frameworks.activity_logs
   * @subpackage models
   */
  class ParentCreatedActivityLogCallback extends ParentActivityLogCallback {
    
    /**
     * Construct callback
     */
    function __construct() {
    	$interface = AngieApplication::getPreferedInterface();

      $this->setRssSubjectWithTarget(lang(':name :type_lowercase created in :target_name :target_type_lowercase'));
      $this->setRssSubjectWithoutTarget(lang(':name :type_lowercase created'));
    	
    	if($interface == AngieApplication::INTERFACE_DEFAULT) {
    	  $this->setWithTargetLang(lang('<a href=":url" title=":name">:name_short</a> :type_lowercase created in <a href=":target_url" title=":target_name">:target_name_short</a> :target_type_lowercase'));
      	$this->setWithoutTargetLang(lang('<a href=":url" title=":name">:name_short</a> :type_lowercase created'));
    	} elseif($interface == AngieApplication::INTERFACE_PHONE) {
    	  $this->setWithTargetLang(lang(':name :type_lowercase created in :target_name :target_type_lowercase'));
      	$this->setWithoutTargetLang(lang(':name :type_lowercase created'));
    	} elseif($interface == AngieApplication::INTERFACE_PRINTER) {
    	  $this->setWithTargetLang(lang(':name :type_lowercase created in :target_name :target_type_lowercase'));
      	$this->setWithoutTargetLang(lang(':name :type_lowercase created'));
    	} // if
      
      $this->setActionNameLang(lang('Created'));
    } // __construct
    
  }