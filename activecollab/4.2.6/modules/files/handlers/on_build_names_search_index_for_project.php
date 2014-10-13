<?php

  /**
   * on_build_names_search_index_for_project event handler
   * 
   * @package activeCollab.modules.files
   * @subpackage handlers
   */

  /**
   * Handle on_build_names_search_index_for_project event
   * 
   * @param NamesSearchIndex $search_index
   * @param Project $project
   */
  function files_handle_on_build_names_search_index_for_project(NamesSearchIndex &$search_index, Project &$project) {
    $files = DB::execute("SELECT id, type, name, body, visibility FROM " . TABLE_PREFIX . "project_objects WHERE type IN (?) AND project_id = ? AND state >= ?", ProjectAssets::getAssetTypes(), $project->getId(), STATE_VISIBLE);
    
    if($files) {
      $project_id = $project->getId();

      list($comments, $subtasks) = ProjectObjects::getCommentsAndSubtasksForSearch($files, true, false, STATE_VISIBLE);
      
      foreach($files as $file) {
        $file_id = (integer) $file['id'];
        $file_type = $file['type'];

        $visibility = $file['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal';
        
        Search::set($search_index, array(
          'class' => $file['type'], 
          'id' => (integer) $file['id'], 
        	'context' => "projects:projects/$project_id/files/$visibility/$file[id]", 
          'name' => $file['name'],
          'body' => $file['body'] ? $file['body'] : null,
          'visibility' => $file['visibility'],
          'comments' => isset($comments[$file_type]) && $comments[$file_type][$file_id] ? $comments[$file_type][$file_id] : '',
        ));
      } // foreach
    } // if
  } // files_handle_on_build_names_search_index_for_project