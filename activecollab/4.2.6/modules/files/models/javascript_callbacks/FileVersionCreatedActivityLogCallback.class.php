<?php

  /**
   * File version activity log callback
   * 
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class FileVersionCreatedActivityLogCallback extends ParentActivityLogCallback {
    
    /**
     * Construct callback
     */
    function __construct() {
    	$interface = AngieApplication::getPreferedInterface();

      $this->setRssSubjectWithTarget(lang('New version of :name file in :target_name project uploaded'));
      $this->setRssSubjectWithoutTarget(lang('New version of :name file uploaded'));
    	
    	if($interface == AngieApplication::INTERFACE_DEFAULT) {
    		$this->setWithTargetLang(lang('New version of <a href=":url" title=":name">:name_short</a> file in <a href=":target_url" title=":target_name">:target_name_short</a> project uploaded'));
      	$this->setWithoutTargetLang(lang('New version of <a href=":url" title=":name">:name_short</a> file uploaded'));
    	} elseif($interface == AngieApplication::INTERFACE_PHONE) {
    		$this->setWithTargetLang(lang('New version of :name file in :target_name project uploaded'));
      	$this->setWithoutTargetLang(lang('New version of :name file uploaded'));
    	} elseif($interface == AngieApplication::INTERFACE_PRINTER) {
    		$this->setWithTargetLang(lang('New version of :name file in :target_name project uploaded'));
      	$this->setWithoutTargetLang(lang('New version of :name file uploaded'));
    	} // if
      
      $this->setActionNameLang(lang('New Version'));
    } // __construct
    
  }