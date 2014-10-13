<?php

  /**
   * Invoice issued activity log callback
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class InvoiceIssuedActivityLogCallback extends ParentActivityLogCallback {
  
    /**
     * Construct callback
     */
    function __construct() {
    	$interface = AngieApplication::getPreferedInterface();

      $this->setRssSubjectWithoutTarget(lang(':name invoice issued'));
    	
    	if($interface == AngieApplication::INTERFACE_DEFAULT) {
    		$this->setWithoutTargetLang(lang('<a href=":url" title=":name">:name_short</a> invoice issued'));
    	} elseif($interface == AngieApplication::INTERFACE_PHONE) {
    		$this->setWithoutTargetLang(lang(':name invoice issued'));
    	} elseif($interface == AngieApplication::INTERFACE_PRINTER) {
    		$this->setWithoutTargetLang(lang(':name invoice issued'));
    	} // if
      
      $this->setActionNameLang(lang('Issued'));
    } // __construct
    
  }