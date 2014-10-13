<?php
  
  /**
   * Incoming mail action - Add comment
   * 
   * @package angie.framework.email
   * @subpackage models.incoming_mail_actions
   *
   */
  class IncomingMailCommentAction extends IncomingMailProjectObjectAction {

    const ADD_ON_TASK = 'task';
    const ADD_ON_DISCUSSION = 'discussion';
    const ADD_ON_TEXT_DOCUMENT = 'text_document';

    /**
     * Required additional settings needed for actions
     * 
     * @var array
     */
     protected $required_additional_settings = array();
    
    /*
     * Constructor
     */
    function __construct() {
      $this->setActionClassName(__CLASS__);
      $this->setSettings();
    }//__construct
    
    /**
     * Set settings as name, descriptions..
     */
    public function setSettings() {
      $this->setName(lang('Add New Comment'));
      $this->setDescription(lang('Add new comment to project object.'));
      $this->setTemplateName('incoming_mail_add_comment_action');
      $this->setCanUse(true);
      $this->setModuleName(COMMENTS_FRAMEWORK);
    }//setSettings

    /**
     * Render project elements into filter form
     * 
     */
    function renderProjectElements(IUser $user, Project $project, IncomingMailFilter $filter = null) {
      if($filter instanceof IncomingMailFilter) {
        //set initial values from this array
        $this->action_parameters = $filter->getActionParameters();
      }//if

      $this->elements .= $this->addObjectByTypeSelect($user, $project);

      return $this->elements;
    }//renderProjectElements
    
    /**
     * Do actions over incoming email
     *
     * @param IncomingMail $incoming_email
     * @param mixed $additional_settings
     * @param boolean $force
     * @return Comment
     * @throws Error
     */
    public function doActions(IncomingMail $incoming_email, $additional_settings = false, $force = false) {
      //check all parameters
      $this->checkActionsParameters($incoming_email,$additional_settings);

      if(!$incoming_email->isReplyToNotification()) {
        switch ($additional_settings['type_name']) {
          case self::ADD_ON_TASK:
            $parent_type = 'Task';
            break;
          case self::ADD_ON_DISCUSSION:
            $parent_type = 'Discussion';
            break;
          case self::ADD_ON_TEXT_DOCUMENT:
            $parent_type = 'TextDocument';
            break;
        } //switch
        $parent_id = $additional_settings['object_id'];
        $parent = DataObjectPool::get($parent_type, $parent_id);
      } else {
        //if it is "reply" but marked as conflict for some reason
        $parent = $incoming_email->getParent();
      } //if
      
      if (!$parent instanceof ApplicationObject || ($parent instanceof IState && $parent->getState() == STATE_DELETED)) {
        // parent object does not exist
        throw new Error(IncomingMessageImportErrorActivityLog::ERROR_PARENT_NOT_EXISTS);
      } // if

      //is enough disk space for importing attachments
      if(!DiskSpace::canImportEmailBasedOnDiskLimitation($incoming_email)) {
        throw new Error(IncomingMessageImportErrorActivityLog::ERROR_DISK_QUOTA_REACHED);
      } //if

      if(!$incoming_email->getCreatedById() == 0) {
         $user = Users::findById($incoming_email->getCreatedById());
      } else {
        $user = new AnonymousUser($incoming_email->getCreatedByName(), $incoming_email->getCreatedByEmail());
      }//if

      $allow_for_everyone = $additional_settings['allow_for_everyone'];
      if(!$force && $allow_for_everyone == IncomingMailFilter::ALLOW_FOR_PEOPLE_WHO_CAN) {
        //directly from mailbox - on frequently
        if (!$parent->comments()->canComment($user) && ($parent instanceof ISubscriptions && !$parent->subscriptions()->isSubscribed($user))) {
          // user cannot create comments to parent object
          throw new Error(IncomingMessageImportErrorActivityLog::ERROR_USER_CANNOT_CREATE_COMMENT);
        } //if
      }//if
      $additional_params['set_source'] = OBJECT_SOURCE_EMAIL;
      $comment = $parent->comments()->newComment();

      $attachments = $incoming_email->getAttachments();
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

      if(!$additional_settings['notify_sender']) {
        $additional_params['subscribe_author'] = false;
      } else {
        //get from cc and subscribe them all
        $subscribe_users = $this->getUsersToSubscribe($incoming_email,$additional_settings);
        if($parent instanceof ISubscriptions) {
          $parent->subscriptions()->set($subscribe_users, true);
        } //if
      } //if

      $parent->comments()->submit($incoming_email->getBody(), $user, $additional_params);
      
      return $comment;
    }//doActions
    
  }