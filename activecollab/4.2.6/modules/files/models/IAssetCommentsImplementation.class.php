<?php

  /**
   * Project assets comments implementation
   * 
   * @package activeCollab.modules.assets
   * @subpackage models
   */
  class IAssetCommentsImplementation extends IProjectObjectCommentsImplementation {
  
    /**
     * Construct assets comments implementation
     * 
     * @param ProjectAsset|IComments $object
     * @throws InvalidInstanceError
     */
    function __construct(IComments $object) {
      if($object instanceof ProjectAsset) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'ProjectAsset');
      } // if
    } // __construct

    /**
     * Return code that will tell the application where to route replies to comments
     *
     * @return string
     */
    function getCommentRoutingCode() {
      return 'ASSET/' . $this->object->getId();
    } // getCommentRoutingCode
    
    /**
     * Create a new comment instance
     * 
     * @return AssetComment
     */
    function newComment() {
      $comment = new AssetComment();
      $comment->setParent($this->object);
      
      return $comment;
    } // newComment
    
  }