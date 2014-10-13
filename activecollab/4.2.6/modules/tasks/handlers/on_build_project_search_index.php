<?php

  /**
   * on_build_project_search_index event handler implementation
   * 
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */

  /**
   * on_build_project_search_index event handler 
   * 
   * @param ProjectObjectsSearchIndex $search_index
   * @param Project $project
   * @param array $users_map
   * @param array $milestones_map
   */
  function tasks_handle_on_build_project_search_index(ProjectObjectsSearchIndex &$search_index, Project &$project, &$users_map, &$milestones_map) {
    $tasks = DB::execute("SELECT id, type, category_id, milestone_id, name, body, visibility, assignee_id, priority, due_on, completed_on FROM " . TABLE_PREFIX . "project_objects WHERE type = 'Task' AND project_id = ? AND state >= ?", $project->getId(), STATE_ARCHIVED);
    
    if($tasks) {
      $project_id = $project->getId();
      $project_name = $project->getName();
      
      $item_context = "projects:projects/$project_id/tasks";
      
      $categories_map = Categories::getIdNameMap($project, 'TaskCategory');

      list($comments, $subtasks) = ProjectObjects::getCommentsAndSubtasksForSearch($tasks, true, true);
      
      foreach($tasks as $task) {
        $task_id = (integer) $task['id'];
        $milestone_id = $task['milestone_id'] ? (integer) $task['milestone_id'] : null;
        $category_id = $task['category_id'] ? (integer) $task['category_id'] : null;
        $assignee_id = $task['assignee_id'] ? (integer) $task['assignee_id'] : null;
        
        Search::set($search_index, array(
          'class' => 'Task', 
          'id' => $task_id, 
        	'context' => $item_context . '/' . ($task['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal') . '/' . $task['id'], 
          'project_id' => $project_id, 
  				'project' => $project_name, 
          'milestone_id' => $milestone_id && $milestones_map && isset($milestones_map[$milestone_id]) ? $milestone_id : null, 
          'milestone' => $milestone_id && $milestones_map && isset($milestones_map[$milestone_id]) ? $milestones_map[$milestone_id] : null,
          'category_id' => $category_id && $categories_map && isset($categories_map[$category_id]) ? $category_id : null, 
          'category' => $category_id && $categories_map && isset($categories_map[$category_id]) ? $categories_map[$category_id] : null, 
          'name' => $task['name'], 
          'body' => $task['body'] ? $task['body'] : null,
          'visibility' => $task['visibility'], 
          'assignee_id' => $assignee_id, 
          'assignee' => $assignee_id && isset($users_map[$assignee_id]) ? $users_map[$assignee_id] : null, 
          'priority' => (integer) $task['priority'], 
          'due_on' => $task['due_on'], 
          'completed_on' => $task['completed_on'],
          'comments' => isset($comments['Task']) && $comments['Task'][$task_id] ? trim($comments['Task'][$task_id]) : '',
          'subtasks' => isset($subtasks['Task']) && $subtasks['Task'][$task_id] ? trim($subtasks['Task'][$task_id]) : '',
        ));
      } // foreach
    } // if
  } // tasks_handle_on_build_project_search_index