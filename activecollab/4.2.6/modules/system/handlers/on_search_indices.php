<?php

  /**
   * on_search_indices event handler
   * 
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Register search index
   * 
   * @param array $indices
   * @param SearchProvider $provider
   */
  function system_handle_on_search_indices(&$indices, SearchProvider &$provider) {
    $indices['projects'] = new ProjectsSearchIndex($provider);
    $indices['project_objects'] = new ProjectObjectsSearchIndex($provider);
    $indices['names'] = new NamesSearchIndex($provider);
  } // system_handle_on_search_indices