<?php

  /**
   * on_main_menu event handler
   *
   * @package angie.frameworks.authentication
   * @subpackage handlers
   */
  
  /**
   * Handle on_main_menu event
   *
   * @param MainMenu $menu
   * @param User $user
   */
  function authentication_handle_on_main_menu(MainMenu &$menu, User &$user) {
    if ($menu->isAllowed('users')) {
      $menu->addBefore('users', lang('Users'), Router::assemble('users'), AngieApplication::getImageUrl('main-menu/users.png', AUTHENTICATION_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT), null, 'admin');
    } // if
  } // authentication_handle_on_main_menu