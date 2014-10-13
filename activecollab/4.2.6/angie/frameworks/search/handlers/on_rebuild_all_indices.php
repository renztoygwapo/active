<?php

  /**
   * on_rebuild_all_indices event handler implementation
   * 
   * @package angie.frameworks.search
   * @subpackage handlers
   */

  /**
   * Handle on_rebuild_all_indices event
   * 
   * @param array $steps
   * @param boolean $quick
   */
  function search_handle_on_rebuild_all_indices(&$steps, $quick) {
    foreach(Search::getIndices() as $index) {
      foreach($index->getRebuildSteps() as $step) {
        $steps[$step['url']] = $index->getName() . ' / ' . $step['text'];
      } // foreach
    } // foreach
  } // search_handle_on_rebuild_all_indices