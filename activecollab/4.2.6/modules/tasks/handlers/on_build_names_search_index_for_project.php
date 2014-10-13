<?php

  /**
   * on_build_names_search_index_for_project event handler implementation
   * 
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */

  /**
   * Handle on_build_names_search_index_for_project event
   * 
   * @param NamesSearchIndex $search_index
   * @param Project $project
   */
  function tasks_handle_on_build_names_search_index_for_project(NamesSearchIndex &$search_index, Project &$project) {
    $tasks = DB::execute("SELECT id, type, name, body, visibility, integer_field_1 FROM " . TABLE_PREFIX . "project_objects WHERE type = 'Task' AND project_id = ? AND state >= ?", $project->getId(), STATE_VISIBLE);
    
    if($tasks) {
      $project_id = $project->getId();

      list($comments, $subtasks) = ProjectObjects::getCommentsAndSubtasksForSearch($tasks, true, true, STATE_VISIBLE);
      
      foreach($tasks as $task) {
        $task_id = (integer) $task['id'];
        $visibility = $task['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal';
        
        Search::set($search_index, array(
          'class' => 'Task', 
          'id' => $task_id,
        	'context' => "projects:projects/$project_id/tasks/$visibility/$task_id",
          'name' => $task['name'], 
          'short_name' => '#' . $task['integer_field_1'],
          'body' => $task['body'] ? $task['body'] : null,
          'comments' => isset($comments['Task']) && $comments['Task'][$task_id] ? trim($comments['Task'][$task_id]) : '',
          'subtasks' => isset($subtasks['Task']) && $subtasks['Task'][$task_id] ? trim($subtasks['Task'][$task_id]) : '',
          'visibility' => $task['visibility'],
        ));
      } // foreach
    } // if
  } // tasks_handle_on_build_names_search_index_for_project