<?php

  /**
   * Base Subtask Inspector implementation
   * 
   * @package angie.frameworks.subtasks
   * @subpackage models
   */
  class ISubtaskInspectorImplementation extends IInspectorImplementation {
  	
    /**
     * Do load data for given interface
     * 
     * @param IUser $user
     * @param string $interface
     */
    protected function do_load(IUser $user, $interface) {
      parent::do_load($user, $interface);
      
      // Add assignee property to phone interface
      if($interface == AngieApplication::INTERFACE_PHONE) {
      	$this->addProperty('assignee', lang('Assignee'), new AssigneesInspectorWidget($this->object, null));
      } // if
    } // do_load
    
  } // ISubtaskInspectorImplementation