<?php

  /**
   * Notebooks module on_extend_project_items_type_id_map event handler
   *
   * @package activeCollab.modules.notebooks
   * @subpackage handlers
   */

  /**
   * Handle on_extend_project_items_type_id_map event
   *
   * @param Project $project
   * @param $min_state
   * @param $map
   */
  function notebooks_handle_on_extend_project_items_type_id_map(Project &$project, $min_state, &$map) {
    if(isset($map['Notebook']) && count($map['Notebook'])) {
      foreach($map['Notebook'] as $notebook_id) {
        $notebook_page_ids = NotebookPages::getAllIdsByNotebook($notebook_id);

        if($notebook_page_ids && is_foreachable($notebook_page_ids)) {
          if(isset($map['NotebookPage']) && is_array($map['NotebookPage'])) {
            $map['NotebookPage'] = array_merge($map['NotebookPage'], $notebook_page_ids); // Extend existing list of ID-s
          } else {
            $map['NotebookPage'] = $notebook_page_ids; // First ID-s to add for NotebookPage type
          } // if
        } // if
      } // foreach
    } // if
  } // notebooks_handle_on_extend_project_items_type_id_map