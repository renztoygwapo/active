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
  function tasks_handle_on_object_from_notification_context(&$object, $name, $ids) {
     
    if(strtolower($name) == 'task') {
      $params = explode("/",$ids);
      if(count($params) == 1) {
        //if notifier context TASK/ID
        
        $id = $params[0];
        $object = Tasks::findById($id);
          
      } else {
        //if notifier context TASK/PROJECT_ID/TASK_ID
        
        $project_id = $params[0];
        $task_id = $params[1];
        
        $project = Projects::findById($project_id);
        if($project instanceof Project) {  
          $object = Tasks::findByTaskId($project,$task_id);
        }//if
      }//if
    }//if
    
  } // on_object_from_notification_context