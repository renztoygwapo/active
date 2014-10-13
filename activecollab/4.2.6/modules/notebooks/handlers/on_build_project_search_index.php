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
  function notebooks_handle_on_build_project_search_index(ProjectObjectsSearchIndex &$search_index, Project &$project, &$users_map, &$milestones_map) {
    $notebooks = DB::execute("SELECT id, milestone_id, name, body, visibility FROM " . TABLE_PREFIX . "project_objects WHERE type = 'Notebook' AND project_id = ? AND state >= ?", $project->getId(), STATE_ARCHIVED);
    
    if($notebooks) {
      $project_id = $project->getId();
      $project_name = $project->getName();
      
      $item_context = "projects/$project_id/notebooks";
      
      foreach($notebooks as $notebook) {
        $milestone_id = $notebook['milestone_id'] ? (integer) $notebook['milestone_id'] : null;
        
        $notebook_context = 'projects:' . $item_context . '/' . ($notebook['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal') . '/' . $notebook['id'];
        
        Search::set($search_index, array(
          'class' => 'Notebook', 
          'id' => (integer) $notebook['id'], 
        	'context' => $notebook_context, 
          'project_id' => $project_id, 
  				'project' => $project_name, 
          'milestone_id' => $milestone_id && isset($milestones_map[$milestone_id]) ? $milestone_id : null, 
          'milestone' => $milestone_id && isset($milestones_map[$milestone_id]) ? $milestones_map[$milestone_id] : null,  
          'name' => $notebook['name'], 
          'body' => $notebook['body'] ? $notebook['body'] : null,
          'visibility' => $notebook['visibility'],  
        ));
        
        $page_ids = NotebookPages::getAllIdsByNotebook($notebook['id']);
        if(is_foreachable($page_ids)) {
          $comments = NotebookPages::getCommentsForSearch($page_ids, STATE_ARCHIVED);

          $notebook_pages = DB::execute('SELECT id, name, body FROM ' . TABLE_PREFIX . 'notebook_pages WHERE id IN (?) AND state >= ?', $page_ids, STATE_ARCHIVED);
          if($notebook_pages) {
            foreach($notebook_pages as $notebook_page) {
              $notebook_page_id = (integer) $notebook_page['id'];

              Search::set($search_index, array(
                'class' => 'NotebookPage', 
                'id' => $notebook_page_id,
              	'context' => $notebook_context . '/pages/' . $notebook_page_id,
                'project_id' => $project_id, 
        				'project' => $project_name, 
                'milestone_id' => $milestone_id && isset($milestones_map[$milestone_id]) ? $milestone_id : null, 
                'milestone' => $milestone_id && isset($milestones_map[$milestone_id]) ? $milestones_map[$milestone_id] : null,  
                'name' => $notebook_page['name'], 
                'body' => $notebook_page['body'] ? $notebook_page['body'] : null,
                'comments' => isset($comments[$notebook_page_id]) ? $comments[$notebook_page_id] : '',
              ));
            } // foreach
          } // if
        } // if
      } // foreach
    } // if
  } // notebooks_handle_on_build_project_search_index