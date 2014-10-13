<?php

  /**
   * on_build_project_search_index event handler implementation
   * 
   * @package activeCollab.modules.files
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
  function files_handle_on_build_project_search_index(ProjectObjectsSearchIndex &$search_index, Project &$project, &$users_map, &$milestones_map) {
    $files = DB::execute("SELECT id, type, category_id, milestone_id, name, body, visibility FROM " . TABLE_PREFIX . "project_objects WHERE type IN (?) AND project_id = ? AND state >= ?", ProjectAssets::getAssetTypes(), $project->getId(), STATE_ARCHIVED);
    
    if($files) {
      $project_id = $project->getId();
      $project_name = $project->getName();
      
      $item_context = "projects:projects/$project_id/files";
      
      $categories_map = Categories::getIdNameMap($project, 'AssetCategory');

      list($comments, $subtasks) = ProjectObjects::getCommentsAndSubtasksForSearch($files, true);
      
      foreach($files as $file) {
        $file_id = (integer) $file['id'];
        $file_type = $file['type'];

        $milestone_id = $file['milestone_id'] ? (integer) $file['milestone_id'] : null;
        $category_id = $file['category_id'] ? (integer) $file['category_id'] : null;
        
        Search::set($search_index, array(
          'class' => $file_type,
          'id' => $file_id,
        	'context' => $item_context . '/' . ($file['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal') . '/' . $file['id'], 
          'project_id' => $project_id, 
  				'project' => $project_name, 
          'milestone_id' => $milestone_id && $milestones_map && isset($milestones_map[$milestone_id]) ? $milestone_id : null, 
          'milestone' => $milestone_id && $milestones_map && isset($milestones_map[$milestone_id]) ? $milestones_map[$milestone_id] : null,
          'category_id' => $category_id && $categories_map && isset($categories_map[$category_id]) ? $category_id : null, 
          'category' => $category_id && $categories_map && isset($categories_map[$category_id]) ? $categories_map[$category_id] : null, 
          'name' => $file['name'], 
          'body' => $file['body'] ? $file['body'] : null,
          'visibility' => $file['visibility'],
          'comments' => isset($comments[$file_type]) && $comments[$file_type][$file_id] ? $comments[$file_type][$file_id] : '',
        ));
      } // foreach
    } // if
  } // files_handle_on_build_project_search_index