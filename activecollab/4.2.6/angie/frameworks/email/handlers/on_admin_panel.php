<?php

  /**
   * on_admin_panel event handler
   * 
   * @package angie.framework.email
   * @subpackage handlers
   */

  /**
   * Handle on_admin_panel event
   * 
   * @param AdminPanel $admin_panel
   */
  function email_handle_on_admin_panel(AdminPanel &$admin_panel) {
    $admin_panel->addToGeneral('email', lang('Email'), Router::assemble('email_admin'), AngieApplication::getImageUrl('admin_panel/email.png', EMAIL_FRAMEWORK));

    if (!AngieApplication::isOnDemand()) {
      $admin_panel->addToTools('email_to_comment', lang('Email Reply to Comment'), Router::assemble('email_admin_reply_to_comment'), AngieApplication::getImageUrl('admin_panel/email-to-comment.png', EMAIL_FRAMEWORK));
    } // if
  } // email_handle_on_admin_panel