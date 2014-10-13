<?php

  /**
   * Tasks module on_object_from_notification_context events handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Return tasks object 
   *
   * @return string
   */
  function system_handle_on_object_from_notification_context(&$object, $name, $id) {
     
    if(strtolower($name) == 'milestone') {
      $object = Milestones::findById($id);
    }//if
    
     if(strtolower($name) == 'project-request') {
      $object = ProjectRequests::findById($id);
      if($object instanceof ProjectRequest) {
        $object->setStatus(ProjectRequest::STATUS_NEW);
      }//if
    }//if
    
  } // on_object_from_notification_context