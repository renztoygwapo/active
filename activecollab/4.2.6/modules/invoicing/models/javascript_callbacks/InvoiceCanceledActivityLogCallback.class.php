<?php

  /**
   * Invoice canceled activity log callback
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class InvoiceCanceledActivityLogCallback extends ParentActivityLogCallback {
  
    /**
     * Construct callback
     */
    function __construct() {
    	$interface = AngieApplication::getPreferedInterface();

      $this->setRssSubjectWithoutTarget(lang(':name invoice canceled'));
    	
    	if($interface == AngieApplication::INTERFACE_DEFAULT) {
    		$this->setWithoutTargetLang(lang('<a href=":url" title=":name">:name_short</a> invoice canceled'));
    	} elseif($interface == AngieApplication::INTERFACE_PHONE) {
    		$this->setWithoutTargetLang(lang(':name invoice canceled'));
    	} elseif($interface == AngieApplication::INTERFACE_PRINTER) {
    		$this->setWithoutTargetLang(lang(':name invoice canceled'));
    	} // if
      
      $this->setActionNameLang(lang('Canceled'));
    } // __construct
    
  }