<?php

  /**
   * Tasks module on_object_from_notification_context events handler
   *
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */
  
  /**
   * Return tasks object 
   *
   * @return string
   */
  function discussions_handle_on_object_from_notification_context(&$object, $name, $id) {
     
    if(strtolower($name) == 'discussion') {
      $object = Discussions::findById($id);
    }//if
    
  } // on_object_from_notification_context