<?php

  /**
   * on_notification_context_view_url event handler
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */

  /**
   * Handle context view URL event
   *
   * @param IUser $user
   * @param Invoice $context
   * @param string $context_view_url
   */
  function invoicing_handle_on_notification_context_view_url(&$user, &$context, &$context_view_url) {
    if($context instanceof Invoice && $user instanceof User) {
      if($user instanceof Client && Invoices::canManageClientCompanyFinances($user->getCompany(), $user)) {
        $context_view_url = $context->getCompanyViewUrl();
      } else {
        $context_view_url = $context->getViewUrl();
      } // if
    } // if
  } // invoicing_handle_on_notification_context_view_url