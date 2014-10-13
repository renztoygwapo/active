<?php

  /**
   * Invoicing module on_main_menu event handler
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */
  
  /**
   * Add options to main menu
   *
   * @param MainMenu $menu
   * @param User $user
   */
  function invoicing_handle_on_main_menu(MainMenu &$menu, User &$user) {
    if ($menu->isAllowed('invoicing')) {

      // Manage owner company invoices
      if($user->isFinancialManager()) {
        $menu->addAfter('invoicing', lang('Invoices'), Router::assemble('invoices'), AngieApplication::getImageUrl('main-menu/invoices.png', INVOICING_MODULE), array('baloon' => Invoices::countOverdue()), 'homepage');

      // See company details. We are not using isManager() method because it would
      // give access to person with people management permissions
      } elseif($user instanceof User && !$user->isOwner() && Invoices::canManageClientCompanyFinances($user->getCompany(), $user)) {
        $menu->addAfter('invoicing', lang('Invoices and Quotes'), Router::assemble('people_company_invoices', array('company_id' => $user->getCompanyId())), AngieApplication::getImageUrl('main-menu/invoices.png', INVOICING_MODULE), array('baloon' => Invoices::countByCompany($user->getCompany(), $user, array(INVOICE_STATUS_ISSUED))), 'homepage');
      } // if

    } // if
  } // invoicing_handle_on_main_menu