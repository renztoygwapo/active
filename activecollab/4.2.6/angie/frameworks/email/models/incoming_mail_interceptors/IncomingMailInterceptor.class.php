<?php
  /**
   * Class IncomingMailInterceptor
   *
   * @package angie.framework.email
   * @subpackage models
   */
  abstract class IncomingMailInterceptor {

    /**
     * Interceptor name
     *
     * @return string
     */
    abstract function getName();

    /**
     * Interceptor message
     *
     * @return string
     */
    abstract function getMessage();

    /**
     * Check to see if email matches this interceptor
     *
     * @param $incoming_mail
     * @return mixed
     */
    abstract function match(IncomingMail $incoming_mail);

    /**
     * Execute interceptor action
     *
     * @param $incoming_mail
     * @return mixed
     */
    abstract function execute(IncomingMail $incoming_mail);

    /**
     * Force executing interceptor
     *
     * @var bool
     */
    private $force = false;


    /**
     * Attach files from incoming mail to $project_object
     *
     * @param $incoming_mail
     * @param $project_object
     * @param bool $check_disk_space
     * @throws Error
     * @throws InvalidInstanceError
     */
    function attachFilesToProjectObject(&$incoming_mail, &$project_object, $check_disk_space = true) {
      if($check_disk_space && !DiskSpace::canImportEmailBasedOnDiskLimitation($incoming_mail)) {
        throw new Error(IncomingMessageImportErrorActivityLog::ERROR_DISK_QUOTA_REACHED);
      } //if

      $attachments = $incoming_mail->getAttachments();
      $formated_attachments = array();
      if (is_foreachable($attachments)) {
        foreach ($attachments as $attachment) {
          $formated_attachments[] = array(
            'path' => INCOMING_MAIL_ATTACHMENTS_FOLDER.'/'.$attachment->getTemporaryFilename(),
            'filename' => $attachment->getOriginalFilename(),
            'type' => strtolower($attachment->getContentType()),
          );
        } // foreach
        if(!$project_object instanceof IAttachments) {
          throw new InvalidInstanceError('project_object', $project_object, 'IAttachments');
        }//if
        $project_object->attachments()->attachFromArray($formated_attachments);
      } // if

    } // attachFilesToProjectObject

    /*
    * Return CC
    *
    * @return array
    */
    function getUsersToSubscribe(IncomingMail $incoming_email) {

      $subscribe_users = array();

      //subsribe users from cc
      if($incoming_email->getCcTo()) {
        $cc_to = $incoming_email->getCcTo();
        foreach($cc_to as $key => $cc_user) {
          $is_mailbox = IncomingMailboxes::findByEmail($cc_user['email']);
          //check to see if mailbox email is added to cc
          if(!$is_mailbox instanceof IncomingMailbox) {
            $cc_to_user = Users::findByEmail($cc_user['email'], true);
            if($cc_to_user instanceof IUser) {
              $subscribe_users[] = $cc_to_user;
            } else {
              $subscribe_users[] = new AnonymousUser($cc_user['name'] ? $cc_user['name'] : $cc_user['email'] ,$cc_user['email']);
            }//if
          }//if
        }//foreach
      }//if

      //subsribe users from bcc
      if($incoming_email->getBccTo()){
        $bcc_to = $incoming_email->getBccTo();
        foreach($bcc_to as $key => $bcc_user) {
          $bcc_to_user = Users::findByEmail($bcc_user['email'], true);
          if($bcc_to_user instanceof User) {
            $subscribe_users[] = $bcc_to_user;
          } else {
            $subscribe_users[] = new AnonymousUser($bcc_user['name'] ? $bcc_user['name'] : $bcc_user['email'] ,$bcc_user['email']);
          }//if
        }//foreach
      }//if

      return $subscribe_users;

    }//getConversationUsers

    /**
     * Get force execution
     *
     * @return $value
     */
    function isForced() {
      return $this->force;
    } //isForced

    /**
     * Set force execution
     *
     * @param $value
     */
    function setIsForced($value) {
      $this->force = $value;
    } //setIsForced

  } //IncomingMailInterceptor