<?php

  /**
   * Project request specific comments implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectRequestCommentsImplementation extends ICommentsImplementation {
    
    /**
     * Construct project Request subscriptions implementation
     *
     * @param IComments $object
     */
    function __construct(IComments $object) {
      if($object instanceof ProjectRequest) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'ProjectRequest');
      } // if
    } // __construct
    
    /**
     * Create a new comment instance
     *
     * @return ProjectRequestComment
     */
    function newComment() {
      $comment = new ProjectRequestComment();
      $comment->setParent($this->object);
      
      return $comment;
    } // newComment
    
    /**
     * Quickly create and submit a comment
     * 
     * Additional features:
     * 
     * - set_source - Set comment source, default is web
     * - attach_uploaded_files - TRUE by default
     * - log_creation - TRUE by default
     * - subscribe_author - TRUE by default
     * - subscribe_users - Optional list of user ID-s that need to be subscribed
     * 
     * @param string $body
     * @param IUser $by
     * @param array $additional
     * @return Comment
     */
    function submit($body, IUser $by, $additional = null) {
      $comment = parent::submit($body, $by, $additional);
      
      $this->object->setLastCommentOn($comment->getCreatedOn());
      
      if(ProjectRequests::canManage($comment->getCreatedBy())) {
        $this->object->setStatus(ProjectRequest::STATUS_REPLIED);
        
        // Take if not already taken
        if($this->object->getTakenBy() == null) {
          $this->object->setTakenBy($by);
        } // if
      } else {
        $this->object->setStatus(ProjectRequest::STATUS_NEW);
      } // if
      
      $this->object->save();
      
      return $comment;
    } // submit
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can comment parent object
     * 
     * @param IUser $user
     * @return boolean
     */
    function canComment(IUser $user) {
      if($user instanceof IUser) {
        return true;
      } else {
        throw new InvalidInstanceError('user', $user, 'IUser');
      } // if
    } // canComment
    
  }