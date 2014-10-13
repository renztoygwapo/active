<?php

  /**
   * on_visible_contexts event handler implementation
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage helpers
   */

  /**
   * Handle on_visible_contexts event
   * 
   * @param IUser $user
   * @param array $contexts
   * @param ApplicationObject $in
   * @param array $exclude_domains
   */
  function invoicing_handle_on_visible_contexts(IUser &$user, &$contexts, $in, $exclude_domains) {
    if($in instanceof ApplicationObject || in_array('invoices', $exclude_domains)) {
      return;
    } // if
    
    if($user instanceof User) {
      if($user->isFinancialManager()) {
        $contexts[] = 'invoices:invoices';
      } else if(Invoices::canManageClientCompanyFinances($user->getCompany(), $user)) {
        $invoice_ids = Invoices::findIdsByCompany($user->getCompany(), $user);
        if($invoice_ids) {
          foreach($invoice_ids as $invoice_id) {
            $contexts[] = "invoices:invoices/$invoice_id";
          } // foreach
        } // if
      } // if
    } // if
  } // invoicing_handle_on_visible_contexts