<?php

  /**
   * on_phone_homescreen event handler
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */

  /**
   * Handle on_phone_homescreen event
   * 
   * @param NamedList $items
   * @param IUser $user
   */
  function invoicing_handle_on_phone_homescreen(NamedList &$items, IUser &$user) {
    if($user->isFinancialManager()) {
      $items->add('invoicing', array(
        'text' => lang('Invoices'),
      	'url' => Router::assemble('invoices'),
      	'icon' => AngieApplication::getImageUrl('icons/homescreen/invoices.png', INVOICING_MODULE, AngieApplication::INTERFACE_PHONE)
      ));
    } elseif($user instanceof User && Invoices::canManageClientCompanyFinances($user->getCompany(), $user)) {
      $items->add('invoicing', array(
      	'text' => lang('Invoices'),
      	'url' => Router::assemble('people_company_invoices', array('company_id' => $user->getCompanyId())),
      	'icon' => AngieApplication::getImageUrl('icons/homescreen/invoices.png', INVOICING_MODULE, AngieApplication::INTERFACE_PHONE)
      ));
    } // if
  } // invoicing_handle_on_phone_homescreen