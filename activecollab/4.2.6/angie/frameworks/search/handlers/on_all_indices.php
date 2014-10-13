<?php

  /**
   * on_all_indices event handler implementation
   * 
   * @package angie.framework.search
   * @subpackage handlers
   */

  /**
   * Handle on_all_indices event
   * 
   * @param array $indices
   */
  function search_handle_on_all_indices(&$indices) {
    foreach(Search::getIndices() as $index) {
      $indices[] = array(
        'name' => lang(':name Search Index', array('name' => $index->getName())), 
        'description' => null, 
        'icon' => AngieApplication::getImageUrl('search-index.png', SEARCH_FRAMEWORK), 
        'size' => $index->calculateSize(), 
        'rebuild_url' => $index->getRebuildUrl(),
      );
    } // foreach
  } // search_handle_on_all_indices