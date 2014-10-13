<?php

  /**
   * Interface that all objects which want to have subtasks attached to them 
   * need to implement
   *
   * @package angie.frameworks.subtasks
   * @subpackage models
   */
  interface ISubtasks {
    
    /**
     * Return subtasks helper for this object
     *
     * @return ISubtasksImplementation
     */
    public function subtasks();
    
  }