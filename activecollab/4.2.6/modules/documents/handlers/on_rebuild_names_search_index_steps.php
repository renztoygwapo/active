<?php

  /**
   * on_rebuild_names_search_index_steps event handler implementation
   * 
   * @package activeCollab.modules.documents
   * @subpackage models
   */

  /**
   * Handle on_rebuild_names_search_index_steps event
   * 
   * @param array $steps
   */
  function documents_handle_on_rebuild_names_search_index_steps(&$steps) {
    $steps[] = array(
      'text' => lang('Build Documents Index'), 
     	'url' => Router::assemble('document_names_search_index_admin_build'),
    );
  } // documents_handle_on_rebuild_names_search_index_steps