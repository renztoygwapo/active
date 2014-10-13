<?php

  /**
   * Handle on_custom_user_permissions event
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */

  /**
   * Populate user permissions
   *
   * @param User $user
   * @param NamedList $permissions
   */
  function invoicing_handle_on_custom_user_permissions(User &$user, NamedList &$permissions) {
    if($user->isAdministrator() || $user->isManager()) {
      $permissions->add('can_manage_finances', array(
        'name' => lang('Manage Finances'),
        'description' => lang('Let user manage invoices and payments, as well as run financial reports'),
      ));

      $permissions->add('can_manage_quotes', array(
        'name' => lang('Manage Quotes'),
        'description' => lang('This permissions enables users to manage quotes within Invoicing module'),
      ));
    } // if

    if($user instanceof Client) {
      $permissions->add('can_manage_client_finances', array(
        'name' => lang('Receive and Pay Invoices'),
        'description' => lang('Let user receive invoices, quotes, reminders, as well as to pay for the invoices online'),
      ));
    } // if
  } // invoicing_handle_on_custom_user_permissions