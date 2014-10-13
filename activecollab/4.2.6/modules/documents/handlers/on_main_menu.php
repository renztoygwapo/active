<?php

  /**
   * Documents module on_main_menu event handler
   *
   * @package activeCollab.modules.documents
   * @subpackage handlers
   */
  
  /**
   * Handle on_main_menu event
   *
   * @param MainMenu $menu
   * @param User $user
   */
  function documents_handle_on_main_menu(MainMenu &$menu, User &$user) {
    if($menu->isAllowed('documents') && Documents::canUse($user)) {
      $menu->addBefore('documents', lang('Documents'), Router::assemble('documents'), AngieApplication::getImageUrl('main-menu/documents.png', DOCUMENTS_MODULE), null, 'admin');
    } // if
  } // documents_handle_on_main_menu