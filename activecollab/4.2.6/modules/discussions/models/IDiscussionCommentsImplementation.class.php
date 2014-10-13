<?php

  /**
   * Discussion comments implementation
   * 
   * @package activeCollab.modules.discussions
   * @subpackage models
   */
  class IDiscussionCommentsImplementation extends IProjectObjectCommentsImplementation {
    
    /**
     * Construct discussion comments implementation
     * 
     * @param Discussion $object
     */
    function __construct(IComments $object) {
      if($object instanceof Discussion) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'Discussion');
      } // if
    } // __construct
    
    /**
     * Create a new comment instance
     * 
     * @return DiscussionComment
     */
    function newComment() {
      $comment = new DiscussionComment();
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
      $this->object->setLastCommentById($by->getId());
      $this->object->save();
      
      return $comment;
    } // submit
    
  }