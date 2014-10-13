<?php

  /**
   * on_context_domains event handler implementation
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage helpers
   */

  /**
   * Handle on_context_domains event
   * 
   * @param IUser $user
   * @param array $contexts
   */
  function invoicing_handle_on_context_domains(IUser &$user, &$contexts) {
    if($user instanceof User && ($user->isFinancialManager() || Invoices::canManageClientCompanyFinances($user->getCompany(), $user))) {
      $contexts[] = 'invoices';
    } // if
  } // invoicing_handle_on_context_domains