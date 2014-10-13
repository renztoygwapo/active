<?php

  /**
   * Quote specific comments implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class IQuoteCommentsImplementation extends ICommentsImplementation {
    
    /**
     * Construct quote comments implementation
     *
     * @param IComments $object
     */
    function __construct(IComments $object) {
      if($object instanceof Quote) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'Quote');
      } // if
    } // __construct
    
    /**
     * Create a new comment instance
     *
     * @return QuoteComment
     */
    function newComment() {
      $comment = new QuoteComment();
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
        if($this->isLocked() || ($this->object->getState() < STATE_VISIBLE))  {
          return false;
        } // if

        return $this->object->canView($user);
      } else {
        throw new InvalidInstanceError('user', $user, 'IUser');
      } // if
    } // canComment

    /**
     * Since Quotes cannot be edited when they have won or lost status, we need to
     * override default method in ICommentsImplementation in order to be able to
     * lock/unlock comments on those Quotes
     *
     * @param IUser $user
     * @return boolean
     */
    function canChangeLockedState(IUser $user) {
      if($user instanceof IUser) {
        return Quotes::canManage($user);
      } else {
        throw new InvalidInstanceError('user', $user, 'IUser');
      } // if
    } // canChangeLockedState
    
  }