<?php

  // Build on top of email admin controller
  AngieApplication::useController('email_admin', EMAIL_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level email to comment controller implementation
   *
   * @package angie.frameworks.email
   * @subpackage controllers
   */
  abstract class FwEmailToCommentController extends EmailAdminController {

    /**
     * Execute before all actions
     */
    function __before() {
      parent::__before();

      if (AngieApplication::isOnDemand()) {
        $this->response->badRequest();
      } // if
    } // __before

    /**
     * Show index page
     */
    function index() {
      $from_email = AngieApplication::mailer()->getDefaultSender()->getEmail();

      // Mailbox tests
      if(extension_loaded('imap')) {
        $imap_enabled = true;

        $mailbox = IncomingMailboxes::findByEmail($from_email);

        if($mailbox instanceof IncomingMailbox) {
          $mailbox_configured = true;

          if($mailbox->getIsEnabled()) {
            $mailbox_enabled = true;

            if($mailbox->getLastStatus() != IncomingMailbox::LAST_CONNECTION_STATUS_ERROR) {
              $mailbox_last_status_ok = true;
            } else {
              $mailbox_last_status_ok = false;
              $mailbox_next_step = array(
                'text' => lang('Test Connection'),
                'url' => Router::assemble('email_admin_reply_to_comment_update_mailbox', array('mailbox_email_address' => $from_email)),
                'mode' => 'flyout_form',
                'flyout_width' => 500,
                'success_event' => 'incoming_mailbox_updated',
                'error_event' => 'incoming_mailbox_updated_error'
              );
            } // if
          } else {
            $mailbox_enabled = $mailbox_last_status_ok = false;
            $mailbox_next_step = array(
              'text' => lang('Test Connection'),
              'url' => Router::assemble('email_admin_reply_to_comment_update_mailbox', array('mailbox_email_address' => $from_email)),
              'mode' => 'flyout_form',
              'flyout_width' => 500,
              'success_event' => 'incoming_mailbox_updated',
            	'error_event' => 'incoming_mailbox_updated_error'
            );
          } // if
        } else {
          $mailbox_configured = $mailbox_enabled = $mailbox_last_status_ok = false;
          $mailbox_next_step = array(
            'text' => lang('Define Mailbox'),
            'url' => Router::assemble('incoming_email_admin_mailbox_add', array('default_email_address' => $from_email)),
            'mode' => 'flyout_form',
            'success_event' => 'incoming_mailbox_created',
          	'error_event' => 'incoming_mailbox_updated_error'
          );
        } // if
      } else {
        $imap_enabled = $mailbox_configured = $mailbox_enabled = $mailbox_last_status_ok = false;

        $mailbox_next_step = array(
          'text' => lang('Enable IMAP Extension'),
          'url' => Router::assemble('email_admin_reply_to_comment_install_imap'),
          'flyout_width' => 500,
        );
      } // if

      $mailbox_steps = array(
        array(
          'text' => $imap_enabled ? lang('IMAP extension is installed and enabled') : lang('IMAP extension is not installed'),
          'is_ok' => $imap_enabled,
        ), array(
          'text' => $mailbox_configured ?
            lang('Mailbox that checks :reply_to_address address is configured', array('reply_to_address' => $from_email)) :
            lang('Mailbox that checks :reply_to_address address is not configured', array('reply_to_address' => $from_email)),
          'is_ok' => $imap_enabled && $mailbox_configured,
        )
      );

      if($imap_enabled && $mailbox_configured) {
        $mailbox_steps[] = array(
          'text' => $mailbox_enabled ? lang('Mailbox is enabled') : lang('Mailbox is not enabled'),
          'is_ok' => $imap_enabled && $mailbox_configured && $mailbox_enabled,
        );

        $mailbox_steps[] = array(
          'text' => $mailbox_last_status_ok ? lang('Last mailbox connection was successful or not-checked yet via scheduled task') : lang('Last mailbox connection was not successful'),
          'is_ok' => $imap_enabled && $mailbox_configured && $mailbox_enabled && $mailbox_last_status_ok,
        );
      } // if

      if(AngieApplication::isFrequentlyRunning()) {
        $scheduled_tasks_ok = true;
      } else {
        $scheduled_tasks_ok = false;

        $scheduled_tasks_next_step = array(
          'text' => lang('Configure Scheduled Tasks'),
          'url' => Router::assemble('scheduled_tasks_admin'),
        );
      } // if

      // Join the data together in form that we can look through
      $sections = array(

        // From / Reply-To address
        array(
          'title' => 'Reply-To Address for Notifications',
          'steps' => array(
            array(
              'text' => lang('Reply-To header of email notifications sent by activeCollab will be set to :reply_to_address', array('reply_to_address' => $from_email)),
              'is_ok' => true,
            ),
          ),
          'next_step' => AngieApplication::mailer()->isMessageConfigurationLocked() ? false : array(
            'text' => lang('Change Reply-To Address'),
            'url' => Router::assemble('email_admin_reply_to_comment_change_from_address'),
            'mode' => 'flyout_form',
            'flyout_width' => 500,
            'success_event' => 'from_address_updated',
          ),
          'all_ok' => true,
        ),

        // Mailbox
        array(
          'title' => 'Mailbox that Checks for Replies',
          'steps' => $mailbox_steps,
          'next_step' => isset($mailbox_next_step) ? $mailbox_next_step : false,
          'all_ok' => $imap_enabled && $mailbox_configured && $mailbox_enabled && $mailbox_last_status_ok,
        ),

        // Scheduled tasks
        array(
          'title' => 'Scheduled Task that Triggers Mailbox Connection',
          'steps' => array(
            array(
              'text' => $scheduled_tasks_ok ? lang('Frequently scheduled task has been triggered in last 10 minutes') : lang('Frequently scheduled task has not been triggered in last 10 minutes'),
              'is_ok' => $scheduled_tasks_ok,
            ),
          ),
          'next_step' => isset($scheduled_tasks_next_step) ? $scheduled_tasks_next_step : false,
          'all_ok' => $scheduled_tasks_ok,
        ),

      );

      // Set flyout defaults
      foreach($sections as $k => $section) {
        if(isset($sections[$k]['next_step']) && is_array($sections[$k]['next_step'])) {
          if(!array_key_exists('mode', $sections[$k]['next_step'])) {
            $sections[$k]['next_step']['mode'] = 'flyout';
          } // if

          if(!array_key_exists('flyout_width', $sections[$k]['next_step'])) {
            $sections[$k]['next_step']['flyout_width'] = null;
          } // if

          if(!array_key_exists('success_event', $sections[$k]['next_step'])) {
            $sections[$k]['next_step']['success_event'] = null;
          } // if
          
          if(!array_key_exists('error_event', $sections[$k]['next_step'])) {
            $sections[$k]['next_step']['error_event'] = null;
          } // if
        } // if
      } // foreach
      $this->response->assign('sections', $sections);
    } // index

    /**
     * Change from email address
     */
    function change_from_address() {
      if(AngieApplication::mailer()->isMessageConfigurationLocked()) {
        $this->response->notFound();
      } // if

      $from_data = $this->request->post('from', array(
        'name' => ConfigOptions::getValue('notifications_from_name'),
        'email' => ConfigOptions::getValue('notifications_from_email'),
      ));

      $this->response->assign(array(
        'from_data' => $from_data,
        'admin_email' => ADMIN_EMAIL,
      ));

      if($this->request->isSubmitted()) {
        try {
          $from_name = isset($from_data['name']) && $from_data['name'] ? trim($from_data['name']) : '';
          $from_email = isset($from_data['email']) && $from_data['email'] ? trim($from_data['email']) : '';

          if($from_email && !is_valid_email($from_email)) {
            throw new ValidationErrors(array(
              'email' => lang('Email value is not valid'),
            ));
          } // if

          ConfigOptions::setValue(array(
            'notifications_from_name' => $from_name,
            'notifications_from_email' => $from_email,
          ));

          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } // if
    } // change_from_address

    /**
     * Show install IMAP flyout
     */
    function install_imap() {
      $this->response->assign('imap_installed', extension_loaded('imap'));
    } // install_imap

    /**
     * Update mailbox, in case of connection problems
     */
    function update_mailbox() {
      $mailbox_email_address = $this->request->get('mailbox_email_address');

      $mailbox = $mailbox_email_address ? IncomingMailboxes::findByEmail($mailbox_email_address) : null;

      if($mailbox instanceof IncomingMailbox) {
        $mailbox_data = $this->request->post('mailbox', array(
          'server_type' => $mailbox->getServerType(),
          'host' => $mailbox->getHost(),
          'port' => $mailbox->getPort(),
          'username' => $mailbox->getUsername(),
          'password' => $mailbox->getPassword(),
          'security' => $mailbox->getSecurity(),
          'mailbox' => $mailbox->getMailbox(),
        ));

        $this->response->assign(array(
          'mailbox_data' => $mailbox_data,
          'form_action_url' => Router::assemble('email_admin_reply_to_comment_update_mailbox', array('mailbox_email_address' => $mailbox_email_address)),
        ));

        if($this->request->isSubmitted()) {
          try {
            $connection_error = null;

            IncomingMailboxes::testConnection($mailbox_data['host'], $mailbox_data['server_type'], $mailbox_data['security'], $mailbox_data['port'], $mailbox_data['mailbox'], $mailbox_data['username'], $mailbox_data['password'], $connection_error);

            if($connection_error instanceof Exception) {
              throw $connection_error;
            }  // if

            // Save and Enable Mailbox if test connection passes
            $mailbox->setAttributes($mailbox_data);
            $mailbox->setIsEnabled(true);
            $mailbox->setLastStatus(IncomingMailbox::LAST_CONNECTION_STATUS_NOT_CHECKED);
            $mailbox->setFailureAttempts(0);

            $mailbox->save();

            $this->response->respondWithData($mailbox, array(
              'as' => 'incoming_mailbox',
            ));
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // update_mailbox

  }