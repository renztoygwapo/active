<?php

  /**
   * on_incoming_mail_actions event handler implementation
   *
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */

  /**
   * Add email related incoming mail actions
   *
   * @@param NamedList $actions
   * @param IUser $user
   * @param array $unavailable_actions
   */
  function discussions_handle_on_incoming_mail_actions(NamedList &$actions, IUser $user, &$unavailable_actions) {
    if(Projects::countActive($user) > 0) {
      $actions->add('Add Discussion', new IncomingMailDiscussionAction());
    } else {
      $unavailable_actions[] = array(
        'action' => new IncomingMailDiscussionAction(),
        'reason' => lang('This action requires that there is at least one active project'),
      );
    } // if
  } // discussions_handle_on_incoming_mail_actions