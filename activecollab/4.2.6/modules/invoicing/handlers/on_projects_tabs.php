<?php

  /**
   * Invoicing module on_projects_tabs event handler
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
  function invoicing_handle_on_projects_tabs(WireframeTabs &$tabs, IUser &$logged_user) {
    if (Quotes::canManage($logged_user)) {
      $tabs->add('quotes', lang('Quotes'), Router::assemble('quotes'));
    } // if
  } // invoicing_handle_on_projects_tabs