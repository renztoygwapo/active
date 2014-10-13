<?php

  /**
   * Notebook page version activity log callback
   * 
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
  class NotebookPageVersionCreatedActivityLogCallback extends ParentActivityLogCallback {
    
    /**
     * Construct callback
     */
    function __construct() {
    	$interface = AngieApplication::getPreferedInterface();

      $this->setRssSubjectWithTarget(lang('New version of :name page in :target_name notebook created'));
      $this->setRssSubjectWithoutTarget(lang('New version of :name page created'));
    	
    	if($interface == AngieApplication::INTERFACE_DEFAULT) {
    		$this->setWithTargetLang(lang('New version of <a href=":url" title=":name">:name_short</a> page in <a href=":target_url" title=":target_name">:target_name_short</a> notebook created'));
      	$this->setWithoutTargetLang(lang('New version of <a href=":url" title=":name">:name_short</a> page created'));
    	} elseif($interface == AngieApplication::INTERFACE_PHONE) {
    		$this->setWithTargetLang(lang('New version of :name page in :target_name notebook created'));
      	$this->setWithoutTargetLang(lang('New version of :name page created'));
    	} elseif($interface == AngieApplication::INTERFACE_PRINTER) {
    		$this->setWithTargetLang(lang('New version of :name page in :target_name notebook created'));
      	$this->setWithoutTargetLang(lang('New version of :name page created'));
    	} // if
      
      $this->setActionNameLang(lang('New Version'));
    } // __construct
    
  }