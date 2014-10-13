<?php

  /**
   * Framework level application incoming mailer implementation
   *
   * @package angie.frameworks.email
   * @subpackage models.incoming_mail
   */
  class AngieIncomingMailDelegate extends AngieDelegate {

    /**
     * IMAP Mailbox Manager
     *
     * @var PHPImapMailboxManager
     */
    protected $manager;

    /**
     * Filter which is applied over incoming mail
     *
     * @var IncomingMailFilter
     */
    protected $applied_object;


    /**
     * Returns true if environment has necessary libraries to import email
     *
     * @return boolean
     */
    function checkEnvironment() {
      return extension_loaded('imap');
    } // checkEnvironment

    /**
     * Return active mailboxes to check
     *
     * @return IncomingMailboxes[]
     */
    function getActiveMailboxes() {
      return IncomingMailboxes::findAllActive();
    } //getActiveMailboxes

    /**
     * Import emails from mailbox
     *
     * @param int $max_emails
     * @return bool
     */
    function importFromMailboxes($max_emails = 20) {

      $imported_emails_count = 0;

      if (is_foreachable($this->getActiveMailboxes())) {
        foreach ($this->getActiveMailboxes() as $mailbox) {

          try {
            $this->connectToMailbox($mailbox);
          } catch(Error $e) {
            // we didn't connect, so we need to log it
            $this->disableMailbox($mailbox, $e);
            continue;
          } // try

          try {
            //all went well so far, reset failure attempts
            $this->resetFailureAttempts($mailbox);
          } catch (Error $e) {
            ob_start();
            dump_error($e, false);
            $error = ob_get_clean();

            AngieApplication::notifications()
              ->notifyAbout('email/mailbox_not_checked', null)
              ->setError($error)
              ->setMailboxName($mailbox->getDisplayName())
              ->sendToAdministrators(true);
            continue;
          }//try

          $email_count = $this->manager->countMessages();

          for ($mid = 1; $mid < ($email_count+1); $mid++) {
            if ($imported_emails_count >= $max_emails) {
              return true;
            } // if
            $current_message_id = 1;

            //get message
            try {
              $email = $this->manager->getMessage($current_message_id, INCOMING_MAIL_ATTACHMENTS_FOLDER);
            } catch (Error $e) {
              $log = new IncomingMessageServerErrorActivityLog();
              $log->log($mailbox,$e->getMessage());
              continue;
            } // try

            // import email
            if ($this->importEmail($email, $mailbox, $current_message_id)) {
              $imported_emails_count ++;
            } // if
          } // for

        } // foreach
      } // if
    } //check_mailboxes

    /**
     * Imports email from file
     *
     * @param String $filename
     * @return boolean
     */
    function importEmailFromFile($filename) {
      if (!is_file($filename)) {
        return false;
      } // if

      require_once EMAIL_FRAMEWORK_PATH . '/models/incoming_mail_body_processors/IncomingMailBodyProcessor.class.php';

      // message id
      $message_id = 1;

      // initialize connection to mailbox (eml file)
      $this->manager = new PHPImapMailboxManager($filename);
      $this->manager->connect();

      // get the message
      $email = $this->manager->getMessage($message_id, WORK_PATH);
      $this->manager->disconnect();

      // get the email recipient
      $recipient = $email->getAddress();

      $recipient_email = $recipient['email'];
      if (!$recipient_email) {
        throw new Error('Could not extract recipient from email');
      } // if

      // get the mailbox
      $mailbox = IncomingMailboxes::findByEmail($recipient_email);

      // import email
      return $this->importEmail($email, $mailbox, $message_id, true);
    } // importEmailFromFile


    /**
     * Import emails
     *
     * @param $email
     * @param $mailbox
     * @param $current_message_id
     * @param bool $skip_deletion_from_server
     * @return bool
     */
    protected function importEmail(&$email, &$mailbox, &$current_message_id, $skip_deletion_from_server = false) {

      // if email is auto responder delete it
      if($email->getIsAutoRespond()) {
        if (!$skip_deletion_from_server) {
          $this->manager->deleteMessage($current_message_id, true);
        } // if
        $auto_log = new IncomingMessageAutoRespondActivityLog();
        $auto_log->log($email, $mailbox, lang('Auto respond or delivery failure message deleted.'));
        return false;
      }//if

      //import mail into database
      try {
        $pending_email = $this->createPendingEmail($email, $mailbox);
      } catch (Error $e) {
        $import_log = new IncomingMessageServerErrorActivityLog();
        $import_log->log($mailbox,$e->getMessage());
        return false;
      } //

      //delete message from server if possible
      if (!$skip_deletion_from_server) {
        $this->manager->deleteMessage($current_message_id, true);
      } // if

      //apply filters and import incoming mail into activeCollab
      try {
        $imported_object = $this->importPendingEmail($pending_email);
      } catch (Error $e) {
        //make conflict
        $import_error_log = new IncomingMessageImportErrorActivityLog();
        $import_error_log->log($mailbox, $pending_email, $e->getMessage(), $this->applied_object);

        //if there is conflicts and send_instantly is configured
        if(ConfigOptions::getValue('conflict_notifications_delivery') == IncomingMail::CONFLICT_NOTIFY_INSTANTLY) {

          AngieApplication::notifications()
            ->notifyAbout('email/conflict_notify_instantly', null)
            ->setPendingMail($pending_email)
            ->setConflictPageUrl(Router::assemble('incoming_email_admin_conflict'))
            ->setConflictReason($e->getMessage())
            ->sendToAdministrators(true);

        }//if
        return false;
      } //try

      // get the performed action on email
      if($this->applied_object instanceof IncomingMailInterceptor) {
        $performed_action = $this->applied_object;
      } else {
        $performed_action = $this->applied_object->getActionObject();
      } //if

      // log successful import
      $success_log = new IncomingMessageReceivedActivityLog();
      $success_log->log($mailbox, $performed_action, $pending_email,  $this->applied_object, $imported_object);

      // update sender latest activity
      $user = $pending_email->getCreatedBy();
      if ($user instanceof User) {
        try {
          $user->setLastActivityOn(new DateTimeValue());
          $user->save();
        } catch (Exception $e) {
          // do nothing
        } // true
      } // if

      //delete from incoming_emails table
      $pending_email->delete();

      return true;
    } //importEmail

    /**
     * Return interceptors list
     *
     * @return NamedList
     */
    protected function getInterceptors() {
      $interceptors = new NamedList();
      EventsManager::trigger('on_incoming_mail_interceptors', array(&$interceptors));
      return $interceptors;
    } //getInterceptors

    /**
     * Creates pending incoming email from email message
     *
     * @param MailboxManagerEmail $email
     * @param IncomingMailbox $mailbox
     *
     * @return IncomingMail
     */
    protected function createPendingEmail(MailboxManagerEmail &$email, &$mailbox = null) {

      $incoming_mail = new IncomingMail();
      if($mailbox instanceof IncomingMailbox) {
        $incoming_mail->setIncomingMailboxId($mailbox->getId());
      }//if
      $incoming_mail->setHeaders(utf8_encode($email->getHeaders()));

      // object subject
      $subject = $email->getSubject();

      //if reply to notification
      preg_match("/\{(.*?)\/(.*?)\}/is", $subject, $results);
      if (count($results) > 0) {
        $name = $results[1];
        $ids = $results[2];

        $object = null;
        EventsManager::trigger('on_object_from_notification_context', array(&$object, $name, $ids));

        if($object && $object instanceof IComments) {
          $incoming_mail->setParent($object);
          $incoming_mail->setIsReplayToNotification(1);
        }//if

        $subject = trim(str_replace($results[0],'',$subject));
      } // if

      $incoming_mail->setSubject($subject);

      // object body

      $body_procesor = new IncomingMailBodyProcessor($email);

      $mail_body = $body_procesor->extractReply();

      if(strlen_utf($mail_body) > 65000) {
        $content_type = $body_procesor->getBodyProcessedAs();
        $file_extension = $content_type == 'text/plain' ? 'txt' : 'html';
        $file_name = 'message-body.' . $file_extension;
        $file_path = WORK_PATH . '/' . $file_name;

        $file_size = file_put_contents($file_path, $mail_body);
        $mail_body = lang('Email body was too long, so system imported it as attachment. Please download :file_name for details.', array('file_name' => $file_name));
      }//if

      $incoming_mail->setBody($mail_body);
      $incoming_mail->setAdditionalData($body_procesor->getAdditionalData());


      //set to, cc, bcc, replay_to
      if($email->getAddress('to')) {
        $to_email = $email->getAddress('to');
        if(!is_valid_email($to_email['email'])) {
          $to_email['email'] = INCOMING_MAIL_INVALID_EMAIL_ADDRESS;
        } //if
        $incoming_mail->setToEmail(serialize($to_email));
      } //if

      if($email->getAddresses('cc')) {
        $incoming_mail->setCcTo(serialize($email->getAddresses('cc')));
      }
      if($email->getAddresses('bcc')) {
        $incoming_mail->setBccTo(serialize($email->getAddresses('bcc')));
      }
      if($email->getAddresses('reply_to')) {
        $incoming_mail->setReplyTo(serialize($email->getAddresses('reply_to')));
      }

      if ($incoming_mail->getSubject() || $incoming_mail->getBody()) {
        if (!$incoming_mail->getSubject()) {
          $incoming_mail->setSubject(lang('[SUBJECT NOT PROVIDED]'));
        } // if
        if (!$incoming_mail->getBody()) {
          $incoming_mail->setBody(lang('[CONTENT NOT PROVIDED]'));
        } // if
      } // if

      if($email->getPriority() == IncomingMail::IM_PRIORITY_HIGHEST || $email->getPriority() == IncomingMail::IM_PRIORITY_HIGH) {
        $priority = IncomingMailFilter::IM_FILTER_IMPORTANT;
        $incoming_mail->setPriority($priority);
      }  //if


      $sender = $email->getAddress('from');

      // user details
      $email_address = array_var($sender, 'email', null);
      $user = Users::findByEmail($email_address, true);
      if (!$user instanceof User) {
        //if email isn't valid set dummy email
        if(!is_valid_email($email_address)) {
          $incoming_mail->setOriginalFromEmail($email_address);
          $email_address = INCOMING_MAIL_INVALID_EMAIL_ADDRESS;
        }//if
        $user = new AnonymousUser(array_var($sender, 'name', null) ? array_var($sender, 'name', null) : $email_address, $email_address);
      } // if
      $incoming_mail->setCreatedBy($user);

      // creation time
      $incoming_mail->setCreatedOn(new DateTimeValue());

      $incoming_mail->save();

      if($file_size) {
        $body_file = new IncomingMailAttachment();
        $body_file->setTemporaryFilename(basename($file_path));
        $body_file->setOriginalFilename($file_name);
        $body_file->setFileSize($file_size);
        $body_file->setContentType($content_type);
        $body_file->setMailId($incoming_mail->getId());
        $body_file->save();
      }//if

      // create attachment objects
      $attachments = $email->getAttachments();
      if (is_foreachable($attachments)) {
        foreach ($attachments as $attachment) {
          $incoming_attachment = new IncomingMailAttachment();
          $incoming_attachment->setTemporaryFilename(basename(array_var($attachment, 'path', null)));
          $incoming_attachment->setOriginalFilename(array_var($attachment,'filename', null));
          $incoming_attachment->setContentType(array_var($attachment, 'content_type', null));
          $incoming_attachment->setFileSize(array_var($attachment, 'size', null));
          $incoming_attachment->setMailId($incoming_mail->getId());
          $attachment_save = $incoming_attachment->save();
          if (!$attachment_save || is_error($attachment_save)) {
            // we couldn't create object in database so we need to remove file from system
            //@unlink(array_var($attachment,'path'));
          } // if
        } // foreach
      } // if

      return $incoming_mail;
    } // createPendingEmail

    /**
     * Use $incoming_mail as a base for creating ProjectObject
     *
     * @param IncomingMail $incoming_mail
     * @return integer
     */
    protected function importPendingEmail(IncomingMail $incoming_mail) {

      //first try to execute interceptor
      if(is_foreachable($this->getInterceptors())) {
        foreach($this->getInterceptors() as $k => $interceptor) {
          if($interceptor instanceof IncomingMailInterceptor) {
            if($interceptor->match($incoming_mail)) {
              $this->applied_object = $interceptor;
              return $interceptor->execute($incoming_mail);
              break;
            } //if
          } //if
        } //foreach
      } //if

      //then try filters
      $filters = IncomingMailFilters::findAllActive();
      if(is_foreachable($filters)) {
        foreach($filters as $filter) {
          if($filter->match($incoming_mail) !== false) {
            $this->applied_object = $filter;
            return $filter->apply();
            break;
          }//if
        }//foreach
      }//if

      if(AngieApplication::isModuleLoaded('helpdesk')) {
        $this->applied_object = new MailToConversationInterceptor();
        return $this->applied_object->execute($incoming_mail);
      } //if

      throw new Error(IncomingMessageImportErrorActivityLog::ERROR_NO_FILTER_APPLIED);

    } // importPendingEmail

    /**
     * Reset failure attempts on connect
     *
     * @param IncomingMailbox $mailbox
     */
    protected function resetFailureAttempts(IncomingMailbox &$mailbox) {
      $mailbox->setLastStatus(IncomingMailbox::LAST_CONNECTION_STATUS_OK);
      $mailbox->setFailureAttempts(0);
      $mailbox->save();
    } //resetFailureAttempts

    /**
     * Connect to mailbox
     *
     * @param $mailbox
     */
    protected function connectToMailbox(IncomingMailbox &$mailbox) {
      $this->manager = $mailbox->getMailboxManager();
      $this->manager->connect();
    } //connect_to_mailbox

    /**
     * Disable mailbox
     *
     * @param IncomingMailbox $mailbox
     * @param $error
     */
    protected function disableMailbox(IncomingMailbox &$mailbox, $error) {
      $log = new IncomingMessageServerErrorActivityLog();
      $log->log($mailbox,$error->getMessage());

      $mailbox->setLastStatus(IncomingMailbox::LAST_CONNECTION_STATUS_ERROR);
      $mailbox->incrementFailureAttemps();

      if(ConfigOptions::getValue('disable_mailbox_on_successive_connection_failures', false)) {
        //disable mailbox on successive connection failures
        if($mailbox->getFailureAttempts() == ConfigOptions::getValue('disable_mailbox_successive_connection_attempts', false)) {
          $mailbox->setIsEnabled(false);
        } //if
        $mailbox->save();

        if(ConfigOptions::getValue('disable_mailbox_notify_administrator', false) && !$mailbox->getIsEnabled()) {
          AngieApplication::notifications()
            ->notifyAbout('email/mailbox_disabled', null)
            ->setResolveUrl(Router::assemble('incoming_email_admin_mailboxes'))
            ->setMailboxName($mailbox->getDisplayName())
            ->sendToAdministrators(true);
        } // if
      }//if
    } //disableMailbox
    
  }