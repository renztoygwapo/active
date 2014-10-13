<?php

  /**
   * on_inline_tabs handler implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */

  /**
   * Handle on inline tabs event
   *
   * @param NamedList $tabs
   * @param User $logged_user
   * @param Project $project
   * @param array $tabs_settings
   * @return null
   */
  function invoicing_handle_on_inline_tabs(&$tabs, &$object, &$logged_user) {
  	// populate company inline tabs
  	if ($object instanceof Company) {
	  	if(Invoices::canAccessCompanyInvoices($logged_user, $object)) {
	  	   
	      $tabs->add('invoices', array(
	        'title' => lang('Invoices'),
	        'url'   => extend_url(Router::assemble('people_company_invoices', array('company_id' => $object->getId())), array("for_company_profile" => "1")),
	        'count' => Invoices::countByCompany($object, $logged_user)
	      ));

        $tabs->add('quotes', array(
          'title' => lang('Quotes'),
          'url'   => extend_url(Router::assemble('people_company_quotes', array('company_id' => $object->getId())), array('for_company_profile' => "1")),
          'count' => Quotes::countByCompany($object, $logged_user)
        ));
	    } // if
  	} // if Company
  } // invoicing_handle_on_inline_tabs
