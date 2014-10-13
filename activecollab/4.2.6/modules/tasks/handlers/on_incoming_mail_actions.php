<?php

  /**
   * on_incoming_mail_actions event handler implementation
   *
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */

  /**
   * Add email related incoming mail actions
   *
   * @@param NamedList $actions
   * @param IUser $user
   * @param array $unavailable_actions
   */
  function tasks_handle_on_incoming_mail_actions(NamedList &$actions, IUser $user, &$unavailable_actions) {

    if(Projects::countActive($user) > 0) {
      $actions->add('Add Task', new IncomingMailTaskAction());
    } else {
      $unavailable_actions[] = array(
        'action' => new IncomingMailTaskAction(),
        'reason' => lang('This action requires that there is at least one active project'),
      );
    } // if
  } // tasks_handle_on_incoming_mail_actions