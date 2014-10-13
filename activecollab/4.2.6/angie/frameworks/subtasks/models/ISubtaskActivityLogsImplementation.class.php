<?php

  /**
   * Subtask activity logs implementation
   * 
   * @package angie.frameworks.subtasks
   * @subpackage models
   */
  class ISubtaskActivityLogsImplementation extends IActivityLogsImplementation {
    
    /**
     * Return full action string
     * 
     * @param string $action
     * @return string
     */
    function getActionString($action) {
      return "subtask/$action";
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