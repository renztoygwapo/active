<?php

  /**
   * on_incoming_mail_actions event handler implementation
   *
   * @package angie.frameworks.email
   * @subpackage handlers
   */

  /**
   * Add email related incoming mail actions
   *
   * @@param NamedList $actions
   * @param IUser $user
   * @param array $unavailable_actions
   */
  function email_handle_on_incoming_mail_actions(NamedList &$actions, IUser $user, &$unavailable_actions) {
    $actions->add('Move_to_trash', new IncomingMailMoveToTrashAction());
    $actions->add('Ignore', new IncomingMailIgnoreAction());

  } // email_handle_on_incoming_mail_actions