<?php

  /**
   * Invoicing module on_client_invoices_tabs event handler
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */
  
  /**
   * Handle on prepare projects tabs event
   *
   * @param WireframeTabs $tabs
   * @param IUser $logged_user
   */
  function invoicing_handle_on_client_invoices_tabs(WireframeTabs &$tabs, IUser &$logged_user) {
	  if (Invoices::canAccessCompanyInvoices($logged_user, $logged_user->getCompany())) {
      $tabs->add('company_invoices', lang('Invoices'), Router::assemble('people_company_invoices', array('company_id' => $logged_user->getCompany()->getId())));
      $tabs->add("company_quotes", lang('Quotes'), Router::assemble('people_company_quotes', array('company_id' => $logged_user->getCompany()->getId())));
    } // if
  } // invoicing_handle_on_client_invoices_tabs