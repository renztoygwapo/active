<?php

  /**
   * Class ReplyToCommentInterceptor
   *
   * @package angie.frameworks.comments
   * @subpackage models
   */
  class ReplyToCommentInterceptor extends IncomingMailInterceptor{

    /**
     * Interceptor name
     *
     * @return string
     */
    function getName() {
      return lang('Add New Comment Interceptor');
    } //getName

    /**
     * Interceptor message
     *
     * @return string
     */
    function getMessage() {
      return lang('Add New Comment Interceptor');
    } //messgae

    /**
     * Return true if email mathes this interceptor
     *
     * @param $incoming_mail
     * @return mixed|void
     */
    function match(IncomingMail $incoming_mail) {
      return (boolean) $incoming_mail->isReplyToNotification();
    } //match

    /**
     * Execute this interceptor
     *
     * @param $incoming_mail
     * @return mixed|void|Comment
     */
    function execute(IncomingMail $incoming_mail) {

      $parent = $incoming_mail->getParent();

      if (!$parent instanceof ApplicationObject || ($parent instanceof IState && $parent->getState() == STATE_DELETED)) {
        // parent object does not exist
        throw new Error(IncomingMessageImportErrorActivityLog::ERROR_PARENT_NOT_EXISTS);
      } // if

      if(!$this->isForced() && $parent instanceof IComments && $parent->comments()->isLocked()) {
        throw new Error(IncomingMessageImportErrorActivityLog::ERROR_PARENT_NOT_ACCEPTING_COMMENTS);
      } //if

      //is enough disk space for importing attachments
      if(!$this->isForced() && !DiskSpace::canImportEmailBasedOnDiskLimitation($incoming_mail)) {
        throw new Error(IncomingMessageImportErrorActivityLog::ERROR_DISK_QUOTA_REACHED);
      } //if

      if(!$incoming_mail->getCreatedById() == 0) {
        $user = Users::findById($incoming_mail->getCreatedById());
      } else {
        $user = new AnonymousUser($incoming_mail->getCreatedByName(), $incoming_mail->getCreatedByEmail());
      }//if

      if (!$this->isForced() && !$parent->comments()->canComment($user) && ($parent instanceof ISubscriptions && !$parent->subscriptions()->isSubscribed($user))) {
        // user cannot create comments to parent object
        throw new Error(IncomingMessageImportErrorActivityLog::ERROR_USER_CANNOT_CREATE_COMMENT);
      } //if
      $additional_params['set_source'] = OBJECT_SOURCE_EMAIL;

      $comment = $parent->comments()->newComment();

      $attachments = $incoming_mail->getAttachments();
      if (is_foreachable($attachments)) {
        foreach ($attachments as $attachment) {
          $formated_attachments[] = array(
            'path' => INCOMING_MAIL_ATTACHMENTS_FOLDER.'/'.$attachment->getTemporaryFilename(),
            'filename' => $attachment->getOriginalFilename(),
            'type' => strtolower($attachment->getContentType()),
          );
        } // foreach
        if($formated_attachments) {
          $additional_params['attach_files'] = $formated_attachments;
        }//if
      }//if

      $comment = $parent->comments()->submit($incoming_mail->getBody(), $user, $additional_params);

      return $comment;
    } //execute

  } //ReplyToCommentInterceptor