<?php

  /**
   * on_build_names_search_index_for_project event handler implementation
   * 
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */

  /**
   * Handle on_build_names_search_index_for_project event
   * 
   * @param NamesSearchIndex $search_index
   * @param Project $project
   */
  function discussions_handle_on_build_names_search_index_for_project(NamesSearchIndex &$search_index, Project &$project) {
    $discussions = DB::execute("SELECT id, type, name, body, visibility FROM " . TABLE_PREFIX . "project_objects WHERE type = 'Discussion' AND project_id = ? AND state >= ?", $project->getId(), STATE_VISIBLE);
    
    if($discussions) {
      $project_id = $project->getId();

      list($comments, $subtasks) = ProjectObjects::getCommentsAndSubtasksForSearch($discussions, true, false, STATE_VISIBLE);
      
      foreach($discussions as $discussion) {
        $discussion_id = (integer) $discussion['id'];
        $visibility = $discussion['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal';
        
        Search::set($search_index, array(
          'class' => 'Discussion', 
          'id' => $discussion_id,
        	'context' => "projects:projects/$project_id/discussions/$visibility/$discussion_id",
          'name' => $discussion['name'],
          'body' => $discussion['body'] ? $discussion['body'] : null,
          'visibility' => $discussion['visibility'],
          'comments' => isset($comments['Discussion']) && $comments['Discussion'][$discussion_id] ? $comments['Discussion'][$discussion_id] : '',
        ));
      } // foreach
    } // if
  } // discussions_handle_on_build_names_search_index_for_project