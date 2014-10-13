<?php

  /**
   * Calendars framework on_main_menu event handler
   *
   * @package activeCollab.modules.calendars
   * @subpackage handlers
   */
  
  /**
   * Add options to main menu
   *
   * @param MainMenu $menu
   * @param User $user
   */
  function calendars_handle_on_main_menu(MainMenu &$menu, User &$user) {
    if(AngieApplication::isModuleLoaded(CALENDARS_FRAMEWORK_INJECT_INTO) && Calendars::canUse($user)) {
      $menu->addBefore('calendars', lang('Calendars'), Router::assemble('calendars'), AngieApplication::getImageUrl('main-menu/calendars.png', CALENDARS_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT), null, 'admin');
    } // if
  } // calendars_handle_on_main_menu