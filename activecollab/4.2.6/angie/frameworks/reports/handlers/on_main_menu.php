<?php

  /**
   * on_main_menu event handler
   *
   * @package angie.frameworks.reports
   * @subpackage handlers
   */
  
  /**
   * Handle on_main_menu event
   *
   * @param MainMenu $menu
   * @param User $user
   */
  function reports_handle_on_main_menu(MainMenu &$menu, User &$user) {
    if($menu->isAllowed('reports') && $user->canUseReports()) {
      $menu->addBefore('reports', lang('Reports and Filters'), Router::assemble('reports'), AngieApplication::getImageUrl('reports.png', REPORTS_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT), null, 'admin');
    } // if
  } // reports_handle_on_main_menu