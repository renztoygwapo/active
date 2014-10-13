<?php

  /**
   * Notebook pages specific comments implementation
   *
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
  class INotebookPageCommentsImplementation extends ICommentsImplementation {
    
    /**
     * Construct notebook page subscriptions implementation
     *
     * @param IComments $object
     * @throws InvalidInstanceError
     */
    function __construct(IComments $object) {
      if($object instanceof NotebookPage) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'NotebookPage');
      } // if
    } // __construct

    /**
     * Return code that will tell the application where to route replies to comments
     *
     * @return string
     */
    function getCommentRoutingCode() {
      return 'NOTEBOOK/' . $this->object->getNotebookId() . '/' . $this->object->getId();
    } // getCommentRoutingCode
    
    /**
     * Create a new comment instance
     *
     * @return NotebookPageComment
     */
    function newComment() {
      $comment = new NotebookPageComment();
      $comment->setParent($this->object);
      
      return $comment;
    } // newComment
    
  }