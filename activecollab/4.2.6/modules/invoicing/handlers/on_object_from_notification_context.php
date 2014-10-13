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
  function invoicing_handle_on_object_from_notification_context(&$object, $name, $id) {
     
    if(strtolower($name) == 'quote') {
      $object = Quotes::findById($id);
    }//if
    
  } // on_object_from_notification_context