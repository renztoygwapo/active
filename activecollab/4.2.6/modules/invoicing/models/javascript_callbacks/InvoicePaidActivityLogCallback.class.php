<?php

  /**
   * Invoice paid activity log callback
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class InvoicePaidActivityLogCallback extends ParentActivityLogCallback {
  
    /**
     * Construct callback
     */
    function __construct() {
    	$interface = AngieApplication::getPreferedInterface();

      $this->setRssSubjectWithoutTarget(lang(':name invoice paid'));
    	
    	if($interface == AngieApplication::INTERFACE_DEFAULT) {
    		$this->setWithoutTargetLang(lang('<a href=":url" title=":name">:name_short</a> invoice paid'));
    	} elseif($interface == AngieApplication::INTERFACE_PHONE) {
    		$this->setWithoutTargetLang(lang(':name invoice paid'));
    	} elseif($interface == AngieApplication::INTERFACE_PRINTER) {
    		$this->setWithoutTargetLang(lang(':name invoice paid'));
    	} // if
      
      $this->setActionNameLang(lang('Paid'));
    } // __construct
    
  }