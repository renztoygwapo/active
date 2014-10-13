<?php

  /**
   * Return incoming mail interceptors
   *
   * @package angie.framework.comments
   * @subpackage handlers
   */
  function comments_handle_on_incoming_mail_interceptors(NamedList &$interceptors) {

    if(!$interceptors->exists('helpdesk_reply_to')) { //additional check for helpdesk module
      $interceptors->beginWith('reply_to', new ReplyToCommentInterceptor());
    } //if

  } // comments_handle_on_incoming_mail_interceptors