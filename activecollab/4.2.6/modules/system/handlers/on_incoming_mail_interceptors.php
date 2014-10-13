<?php

  /**
   * Return incoming mail interceptors
   *
   * @package activecollab.modules.system
   * @subpackage handlers
   */
  function system_handle_on_incoming_mail_interceptors(NamedList &$interceptors) {

    if(MailToProjectInterceptor::isEnabled()) {
      $interceptors->add('mail_to_project', new MailToProjectInterceptor());
    } //if

  } // system_handle_on_incoming_mail_interceptors