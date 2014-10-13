<?php

  /**
   * Tasks module on_object_from_notification_context events handler
   *
   * @package activeCollab.modules.notebooks
   * @subpackage handlers
   */
  
  /**
   * Return tasks object 
   *
   * @return string
   */
  function notebooks_handle_on_object_from_notification_context(&$object, $name, $ids) {
     
    if(strtolower($name) == 'notebook') {
      $params = explode("/",$ids);
      $notebook_id = $params[0];
      $page_id = $params[1];
      
      $object = NotebookPages::findById($page_id);
    }//if
    
  } // on_object_from_notification_context