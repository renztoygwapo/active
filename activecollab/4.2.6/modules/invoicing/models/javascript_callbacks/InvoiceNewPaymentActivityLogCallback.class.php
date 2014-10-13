<?php

  /**
   * Invoice issued activity log callback
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class InvoiceNewPaymentActivityLogCallback extends ParentActivityLogCallback {
  
    /**
     * Construct callback
     */
    function __construct() {
    	$interface = AngieApplication::getPreferedInterface();

      $this->setRssSubjectWithoutTarget(lang('New payment has been made for :name invoice'));
    	
    	if($interface == AngieApplication::INTERFACE_DEFAULT) {
    		$this->setWithoutTargetLang(lang('New payment has been made for <a href=":url" title=":name">:name_short</a> invoice'));
    	} elseif($interface == AngieApplication::INTERFACE_PHONE) {
    		$this->setWithoutTargetLang(lang('New payment has been made for :name invoice'));
    	} elseif($interface == AngieApplication::INTERFACE_PRINTER) {
    		$this->setWithoutTargetLang(lang('New payment has been made for :name invoice'));
    	} // if
      
      $this->setActionNameLang(lang('New Payment'));
    } // __construct
    
  }