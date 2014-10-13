<?php

  /**
   * Class MailToProjectInterceptor
   *
   * @package activecollab.system.modules
   * @subpackage models.incoming_mail_interceptors
   */
  class MailToProjectInterceptor extends IncomingMailInterceptor {

    const ACTION_TASK = 'task:';
    const ACTION_DISCUSSION = 'discussion:';
    const ACTION_FILES = 'files:';
    const ACTION_DOCUMENT = 'document:';

    const M2P_DELIMITER = '+m2p-';

    /**
     * Interceptor name
     *
     * @return string
     */
    function getName() {
      return lang('Add New Project Object');
    } //getName

    /**
     * Interceptor message
     *
     * @return string
     */
    function getMessage() {
      return lang('Add New Project Object');
    } //messgae

    /**
     * Return true if this interceptor is enabled
     */
    static function isEnabled() {
      return (boolean) ConfigOptions::getValue('mail_to_project');
    } //isEnabled

    /**
     * Return default object instance
     *
     * @return Task|Discussion
     */
    static function getDefaultObject() {
      $default = ConfigOptions::getValue('mail_to_project_default_action');
      if($default == self::ACTION_TASK) {
        return new Task();
      } elseif ($default == self::ACTION_DISCUSSION) {
        return new Discussion();
      } //if
    } //getDefaultObject

    /**
     * Return true if email matches this interceptor
     *
     * @param $incoming_mail
     * @return mixed|void
     */
    function match(IncomingMail $incoming_mail) {
      //first try to match "to" addres - only first one
      $to_email = $incoming_mail->getTo();
      $email = $to_email['email'];
      if((boolean) strpos_utf($email, self::M2P_DELIMITER)) {
        return true;
      } //if

      //if not in "to" then try to find "m2p" in one of the cc addresses
      $cc_emails = $incoming_mail->getCcTo();
      if(is_foreachable($cc_emails)) {
        foreach($cc_emails as $cc_email) {
          if((boolean) strpos_utf($cc_email['email'], self::M2P_DELIMITER)) {
            return true;
          } //if
        } //foreach
      } //if

      return false;
    } //match

    /**
     * Execute this interceptor
     *
     * @param $incoming_mail
     * @param $additional_params
     * @return mixed|void|Project
     */
    function execute(IncomingMail $incoming_mail, $additional_params = null) {

      $subject = $incoming_mail->getSubject();

      //find project
      $to_email = $incoming_mail->getTo();

      if((boolean) strpos_utf($to_email['email'], self::M2P_DELIMITER)) {
        //check if "m2p" code is in "to" address
        $email = $to_email['email'];
      } else {
        //check cc addresses
        $cc_emails = $incoming_mail->getCcTo();
        if(is_foreachable($cc_emails)) {
          foreach($cc_emails as $cc_email) {
            if((boolean) strpos_utf($cc_email['email'], self::M2P_DELIMITER)) {
              $email = $cc_email['email'];
            } //if
          } //foreach
        } //if
      } //if

      $email_parts = explode(MailToProjectInterceptor::M2P_DELIMITER, $email);
      $email_with_code = explode("@", $email_parts[1]);
      $m2p_code = $email_with_code[0];

      $project = Projects::findByMailToProjectCode($m2p_code);

      if(!$project instanceof Project || $project->getState() == STATE_DELETED) {
        throw new Error(IncomingMessageImportErrorActivityLog::ERROR_PROJECT_DOES_NOT_EXISTS);
      }//if

      //check to see if there is enough disk space for importing attachments from this email
      if(!DiskSpace::canImportEmailBasedOnDiskLimitation($incoming_mail)) {
        throw new Error(IncomingMessageImportErrorActivityLog::ERROR_DISK_QUOTA_REACHED);
      } //if

      //get sender
      if(!$incoming_mail->getCreatedById() == 0) {
        $sender = Users::findById($incoming_mail->getCreatedById());
      } else {
        $sender = new AnonymousUser($incoming_mail->getCreatedByName(), $incoming_mail->getCreatedByEmail());
      }//if

      $files = array();

      $check_subject = strtolower($subject);
      if(str_starts_with($check_subject, self::ACTION_TASK)) {
        $object = new Task();
        $name = trim(str_ireplace_first(self::ACTION_TASK, '', $subject));
        $email_tpl = 'tasks/new_task';

      } elseif(str_starts_with($check_subject, self::ACTION_DISCUSSION)) {
        $object = new Discussion();
        $name = trim(str_ireplace_first(self::ACTION_DISCUSSION, '', $subject));
        $email_tpl = 'discussions/new_discussion';

      } elseif(str_starts_with($check_subject, self::ACTION_DOCUMENT) || str_starts_with($check_subject, 'fw')) {
        $object = new TextDocument();
        $email_tpl = 'files/new_text_document';

        if(str_starts_with($check_subject, self::ACTION_DOCUMENT)) {
          $name = trim(str_ireplace_first(self::ACTION_DOCUMENT, '', $subject));
        } elseif(str_starts_with($check_subject, 'fw:')) {
          $name = trim(str_ireplace_first('fw:', '', $subject));
        } elseif(str_starts_with($check_subject, 'fwd:')) {
          $name = trim(str_ireplace_first('fwd:', '', $subject));
        } elseif(str_starts_with($check_subject, 'fwd')) {
          $name = trim(str_ireplace_first('fwd', '', $subject));
        } elseif(str_starts_with($check_subject, 'fw')) {
          $name = trim(str_ireplace_first('fw', '', $subject));
        } //if

      } elseif(str_starts_with($check_subject, self::ACTION_FILES) || str_starts_with($check_subject, 'files')) {
        $object = new File();

        if(str_starts_with($check_subject, self::ACTION_FILES)) {
          $name = trim(str_ireplace_first(self::ACTION_FILES, '', $subject));
        } elseif(str_starts_with($check_subject, 'files')) {
          $name = trim(str_ireplace_first('files', '', $subject));
        } //if
        $email_tpl = 'files/new_file';

      } else {
        //default object
        $object = self::getDefaultObject();
        $name = $subject;
        if($object instanceof Task) {
          $email_tpl = 'tasks/new_task';
        } elseif ($object instanceof Discussion) {
          $email_tpl = 'discussions/new_discussion';
        } //if
      } //if

      if($object instanceof File) {
        //attach files
        $attachments = $incoming_mail->getAttachments();

        if(is_foreachable($attachments)) {
          foreach($attachments as $attachment) {

            $object = new File();

            $object->setVisibility($project->getDefaultVisibility());
            $object->setName($attachment->getOriginalFilename());
            $object->setBody($incoming_mail->getBody());
            $object->setProject($project);
            $object->setSize($attachment->getFileSize());
            $object->setLocation($attachment->getTemporaryFilename());
            $object->setMimeType($attachment->getContentType());
            $object->setState(STATE_VISIBLE);
            $object->setCreatedBy($sender);
            $object->setCreatedOn($incoming_mail->getCreatedOn());
            $object->setVersionNum(1);

            //attach files from mail to task
            $file_path = INCOMING_MAIL_ATTACHMENTS_FOLDER.'/'.$attachment->getTemporaryFilename();

            copy($file_path, UPLOAD_PATH . "/" . $attachment->getTemporaryFilename());
            $object->setMd5(md5_file(UPLOAD_PATH . "/" . $attachment->getTemporaryFilename()));

            $object->save();

            $files[] = $object;
          } //foreach
         } //if

      } else {
        //task,discussion,text document

        //set basic values
        $object->setProject($project);
        $object->setCreatedBy($sender);
        $object->setCreatedOn($incoming_mail->getCreatedOn());
        $object->setVisibility(VISIBILITY_NORMAL);
        $object->setState(STATE_VISIBLE);
        if($object instanceof ILabel) {
          if(Labels::findDefault('AssignmentLabel') instanceof Label) {
            $object->setLabelId(Labels::findDefault('AssignmentLabel')->getId());
          } //if
        } //if
        if($object instanceof Task || $object instanceof Discussion) {
          $object->setSource(OBJECT_SOURCE_EMAIL);
        }//if

        $object->setName(substr($name,0,150));
        $object->setBody($incoming_mail->getBody());
        //attach files from mail to task
        $this->attachFilesToProjectObject($incoming_mail, $object);

        //save object
        $object->save();
      } //if

      //notify sender and subscribe him
      AngieApplication::notifications()
          ->notifyAbout(EMAIL_FRAMEWORK_INJECT_INTO . '/notify_email_sender', $object)
          ->sendToUsers($sender);

      if($object instanceof ISubscriptions) {
        //get users from cc and bcc
        $subscribe_users = $this->getUsersToSubscribe($incoming_mail);

        $subscribe_users[] = $sender;

        $project_leader = $project->getLeader();
        if($project_leader->getId() != $sender->getId()) {
         $subscribe_users[] = $project_leader;
        } //if

        $object->subscriptions()->set($subscribe_users, true);

        if($object->subscriptions()->hasSubscribers()) {

          if(count($files) > 1) {
            AngieApplication::notifications()
              ->notifyAbout('files/multiple_files_uploaded', null, $sender)
              ->setFiles($files)
              ->setProject($project)
              ->sendToUsers($object->subscriptions()->get(), true);
          } else {
            AngieApplication::notifications()
              ->notifyAbout($email_tpl, $object, $sender)
              ->sendToSubscribers();
          } //if

        }//if
      } //if

      return $object;

    } //execute

  } //MailToProjectInterceptor