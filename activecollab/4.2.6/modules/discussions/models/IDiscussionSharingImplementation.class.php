<?php

  /**
   * Discussion sharing implementation
   * 
   * @package activeCollab.modules.discussions
   * @subpackage models
   */
  class IDiscussionSharingImplementation extends ISharingImplementation {
  
    /**
     * Return sharing context
     * 
     * @return string
     */
    function getSharingContext() {
      return Discussions::SHARING_CONTEXT;
    } // getSharingContext
    
    /**
     * Returns true if this implementation has body text to display
     * 
     * @return boolean
     */
    function hasSharedBody() {
      return $this->object->getBody() != '';
    } // hasSharedBody
    
    /**
     * Return prepared shared body
     * 
     * @param Language $language
     * @return string
     */
    function getSharedBody(Language $language) {
      return HTML::toRichText($this->object->getBody());
    } // getSharedBody
    
    /**
     * Returns true if the public page needs to be displayed as a discussion
     * 
     * @return boolean
     */
    function displayAsDiscussion() {
      return true;
    } // displayAsDiscussion
    
  }