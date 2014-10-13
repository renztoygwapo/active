<?php

  /**
   * on_build_project_search_index event handler implementation
   * 
   * @package activeCollab.modules.discussion
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
  function discussions_handle_on_build_project_search_index(ProjectObjectsSearchIndex &$search_index, Project &$project, &$users_map, &$milestones_map) {
    $discussions = DB::execute("SELECT id, type, category_id, milestone_id, name, body, visibility FROM " . TABLE_PREFIX . "project_objects WHERE type = 'Discussion' AND project_id = ? AND state >= ?", $project->getId(), STATE_ARCHIVED);
    
    if($discussions) {
      $project_id = $project->getId();
      $project_name = $project->getName();
      
      $item_context = "projects:projects/$project_id/discussions";
      
      $categories_map = Categories::getIdNameMap($project, 'DiscussionCategory');

      list($comments, $subtasks) = ProjectObjects::getCommentsAndSubtasksForSearch($discussions, true);
      
      foreach($discussions as $discussion) {
        $discussion_id = (integer) $discussion['id'];
        $milestone_id = $discussion['milestone_id'] ? (integer) $discussion['milestone_id'] : null;
        $category_id = $discussion['category_id'] ? (integer) $discussion['category_id'] : null;
        
        Search::set($search_index, array(
          'class' => 'Discussion', 
          'id' => $discussion_id,
        	'context' => $item_context . '/' . ($discussion['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal') . '/' . $discussion['id'], 
          'project_id' => $project_id, 
  				'project' => $project_name, 
          'milestone_id' => $milestone_id && $milestones_map && isset($milestones_map[$milestone_id]) ? $milestone_id : null, 
          'milestone' => $milestone_id && $milestones_map && isset($milestones_map[$milestone_id]) ? $milestones_map[$milestone_id] : null,
          'category_id' => $category_id && $categories_map && isset($categories_map[$category_id]) ? $category_id : null, 
          'category' => $category_id && $categories_map && isset($categories_map[$category_id]) ? $categories_map[$category_id] : null, 
          'name' => $discussion['name'], 
          'body' => $discussion['body'] ? $discussion['body'] : null,
          'visibility' => $discussion['visibility'],
          'comments' => isset($comments['Discussion']) && $comments['Discussion'][$discussion_id] ? $comments['Discussion'][$discussion_id] : '',
        ));
      } // foreach
    } // if
  } // discussions_handle_on_build_project_search_index