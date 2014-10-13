<?php

  /**
   * on_build_names_search_index_for_project event handler implementation
   * 
   * @package activeCollab.modules.notebooks
   * @subpackage handlers
   */

  /**
   * Handle on_build_names_search_index_for_project event
   * 
   * @param NamesSearchIndex $search_index
   * @param Project $project
   */
  function notebooks_handle_on_build_names_search_index_for_project(NamesSearchIndex &$search_index, Project &$project) {
    $notebooks = DB::execute("SELECT id, type, name, body, visibility FROM " . TABLE_PREFIX . "project_objects WHERE type = 'Notebook' AND project_id = ? AND state >= ?", $project->getId(), STATE_VISIBLE);
    
    if($notebooks) {
      $project_id = $project->getId();
      
      foreach($notebooks as $notebook) {
        $notebook_context = 'projects:projects/' . $project_id . '/notebooks/' . ($notebook['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal') . '/' . $notebook['id'];
        
        Search::set($search_index, array(
          'class' => 'Notebook',
          'id' => $notebook['id'], 
        	'context' => $notebook_context, 
          'name' => $notebook['name'],
          'body' => $notebook['body'] ? $notebook['body'] : null,
          'visibility' => $notebook['visibility'], 
        ));
        
        $page_ids = NotebookPages::getAllIdsByNotebook($notebook['id']);
        if(is_foreachable($page_ids)) {
          $comments = NotebookPages::getCommentsForSearch($page_ids, STATE_VISIBLE);

          $notebook_pages = DB::execute('SELECT id, name, body FROM ' . TABLE_PREFIX . 'notebook_pages WHERE id IN (?) AND state >= ?', $page_ids, STATE_VISIBLE);
          if($notebook_pages) {
            foreach($notebook_pages as $notebook_page) {
              $notebook_page_id = (integer) $notebook_page['id'];

              Search::set($search_index, array(
                'class' => 'NotebookPage', 
                'id' => $notebook_page_id,
              	'context' => "$notebook_context/pages/$notebook_page_id",
                'name' => $notebook_page['name'],
                'body' => $notebook_page['body'] ? $notebook_page['body'] : null,
                'comments' => isset($comments[$notebook_page_id]) ? $comments[$notebook_page_id] : '',
                'visibility' => $notebook['visibility'],
              ));
            } // foreach
          } // if
        } // if
      } // foreach
    } // if
  } // notebooks_handle_on_build_names_search_index_for_project