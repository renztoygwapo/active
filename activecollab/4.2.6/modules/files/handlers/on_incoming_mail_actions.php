<?php

  /**
   * on_incoming_mail_actions event handler implementation
   *
   * @package activeCollab.modules.files
   * @subpackage handlers
   */

  /**
   * Add email related incoming mail actions
   *
   * @@param NamedList $actions
   * @param IUser $user
   * @param array $unavailable_actions
   */
  function files_handle_on_incoming_mail_actions(NamedList &$actions, IUser $user, &$unavailable_actions) {
    if(Projects::countActive($user) > 0) {
      $actions->add('Add Files', new IncomingMailFileAction());
      $actions->add('Add Text Document', new IncomingMailTextDocumentAction());
    } else {
      $unavailable_actions[] = array(
        'action' => new IncomingMailFileAction(),
        'reason' => lang('This action requires that there is at least one active project'),
      );
      $unavailable_actions[] = array(
        'action' => new IncomingMailTextDocumentAction(),
        'reason' => lang('This action requires that there is at least one active project'),
      );
    } // if
  } // files_handle_on_incoming_mail_actions