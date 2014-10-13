<?php

  /**
   * Comment activity logs helper implementation
   * 
   * @package angie.frameworks.comments
   * @subpackage models
   */
  class ICommentActivityLogsImplementation extends IActivityLogsImplementation {
  
    /**
     * Return full action string
     * 
     * @param string $action
     * @return string
     */
    function getActionString($action) {
      return "comment/$action";
    } // getActionString
    
    /**
     * Return target for given action
     * 
     * @param string $action
     * @return ApplicationObject
     */
    function getTarget($action = null) {
      return $this->object->getParent();
    } // getTarget
    
  }