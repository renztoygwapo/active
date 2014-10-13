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
   * @param array $ignore_contexts
   * @param ApplicationObject $in
   * @param array $include_domains
   */
  function invoicing_handle_on_visible_contexts(IUser &$user, &$contexts, &$ignore_contexts, $in, $include_domains) {
    if($user instanceof User && $user->isFinancialManager() && empty($in) && ($include_domains === null || in_array('invoices', $include_domains))) {
      $contexts[] = 'invoices:invoices%';
    } // if
  } // invoicing_handle_on_visible_contexts