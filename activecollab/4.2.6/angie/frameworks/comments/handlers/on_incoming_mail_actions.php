<?php

  /**
   * on_incoming_mail_actions event handler implementation
   *
   * @package angie.frameworks.comments
   * @subpackage handlers
   */

  /**
   * Add email related incoming mail actions
   *
   * @@param NamedList $actions
   * @param IUser $user
   * @param array $unavailable_actions
   */
  function comments_handle_on_incoming_mail_actions(NamedList &$actions, IUser $user, &$unavailable_actions) {

    if (AngieApplication::getName() == 'activeCollab') {
      if(Projects::countActive($user) > 0) {
        $actions->add('add_new_comment', new IncomingMailCommentAction());
      } else {
        $unavailable_actions[] = array(
          'action' => new IncomingMailCommentAction(),
          'reason' => lang('This action requires that there is at least one active project'),
        );
      } // if
    } // if
  } // tasks_handle_on_incoming_mail_actions