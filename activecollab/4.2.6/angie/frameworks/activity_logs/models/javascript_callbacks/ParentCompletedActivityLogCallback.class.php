<?php

  /**
   * Parent completed activity log callback
   * 
   * @package angie.frameworks.activity_logs
   * @subpackage models
   */
  class ParentCompletedActivityLogCallback extends ParentActivityLogCallback {
    
    /**
     * Construct callback
     */
    function __construct() {
    	$interface = AngieApplication::getPreferedInterface();

      $this->setRssSubjectWithTarget(lang(':name :type_lowercase in :target_name :target_type_lowercase completed'));
      $this->setRssSubjectWithoutTarget(lang(':name :type_lowercase completed'));
    	
    	if($interface == AngieApplication::INTERFACE_DEFAULT) {
    		$this->setWithTargetLang(lang('<a href=":url" title=":name">:name_short</a> :type_lowercase in <a href=":target_url" title=":target_name">:target_name_short</a> :target_type_lowercase completed'));
      	$this->setWithoutTargetLang(lang('<a href=":url" title=":name">:name_short</a> :type_lowercase completed'));
    	} elseif($interface == AngieApplication::INTERFACE_PHONE) {
    	  $this->setWithTargetLang(lang(':name :type_lowercase in :target_name :target_type_lowercase completed'));
      	$this->setWithoutTargetLang(lang(':name :type_lowercase completed'));
    	} elseif($interface == AngieApplication::INTERFACE_PRINTER) {
    	  $this->setWithTargetLang(lang(':name :type_lowercase in :target_name :target_type_lowercase completed'));
      	$this->setWithoutTargetLang(lang(':name :type_lowercase completed'));
    	} // if // if
      
      $this->setActionNameLang(lang('Completed'));
    } // __construct
    
  }