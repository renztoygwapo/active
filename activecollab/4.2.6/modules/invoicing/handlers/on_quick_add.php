<?php

  /**
   * Invoicing module on_quick_add event handler
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */
  
  /**
   * Handle on quick add event
   *
   * @param NamedList $items
   * @param NamedList $subitems
   * @param array $map
   * @param User $logged_user
   * @param DBResult $projects 
   * @param DBResult $companies
   * @param string $interface
   */
  function invoicing_handle_on_quick_add($items, $subitems, &$map, $logged_user, $projects, $companies, $interface = AngieApplication::INTERFACE_DEFAULT) {
  	if($interface == AngieApplication::INTERFACE_DEFAULT) {
  		if(Invoices::canAdd($logged_user)) {
		    $items->add('invoice', array(
		      'text' => lang('Invoice'),
		    	'title' => lang('Create Invoice'),
		      'icon' => AngieApplication::getImageUrl('icons/32x32/invoice.png', INVOICING_MODULE),
		      'url' => Router::assemble('invoices_add'),
		    	'group' => 'invoicing',
		    	'event' => 'invoice_created',
		    ));
	  	} // if
	  	
	  	if(Quotes::canAdd($logged_user)) {
	  		$items->add('quote', array(
		      'text' => lang('Quote'),
	  			'title' => lang('Create Quote'),
		      'icon' => AngieApplication::getImageUrl('icons/32x32/quote.png', INVOICING_MODULE),
		      'url' => Router::assemble('quotes_add'),
		    	'group' => 'invoicing',
		    	'event' => 'quote_created',  		
	  		));
	  	} // if
	  	
	  	if(RecurringProfiles::canAdd($logged_user)) {
	  		$items->add('recurring_profile', array(
		      'text' => lang('Recurring Profile'),
	  			'title' => lang('Create Recurring Profile'),
		      'icon' => AngieApplication::getImageUrl('icons/32x32/recurring-profile.png', INVOICING_MODULE),
		      'url' => Router::assemble('recurring_profile_add'),
		    	'group' => 'invoicing',
		    	'event' => 'recurring_profile_created',  		
	  		));
	  	} // if
  	} // if
  	
  } // invoicing_handle_on_project_tabs