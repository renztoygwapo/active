<?php

  /**
   * Project asset sharing implementation
   * 
   * @package activeCollab.modules.files
   * @subpackage models
   */
  abstract class IProjectAssetSharingImplementation extends ISharingImplementation {
  
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
     * @return string
     */
    function getSharedBody() {
      return HTML::toRichText($this->object->getBody());
    } // getSharedBody
    
  }