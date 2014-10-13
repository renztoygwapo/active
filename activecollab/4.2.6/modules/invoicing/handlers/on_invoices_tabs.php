<?php

  /**
   * Invoicing module on_invoices_tabs event handler
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */
  
  /**
   * Handle on prepare invoicing tabs event
   *
   * @param WireframeTabs $tabs
   * @param IUser $logged_user
   */
  function invoicing_handle_on_invoices_tabs(WireframeTabs &$tabs, IUser &$logged_user) {
  	if($logged_user->isFinancialManager()) {
		  $tabs->add('recurring_profiles', lang('Recurring Profiles'), Router::assemble('recurring_profiles'));
		  //$tabs->add('recurring_approval_requests', lang('Approval Requests'), RecurringApprovalRequest::getMainPageUrl());
  	} // if
  } // invoicing_handle_on_invoices_tabs