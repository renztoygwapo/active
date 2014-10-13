<?php

  /**
   * Handle on_extra_stats event
   *
   * @package activeCollab.modules.notebooks
   * @subpackage handlers
   */

  /**
   * Populate extra stats
   *
   * @param array $stats
   */
  function notebooks_handle_on_extra_stats(&$stats) {
    $stats['npages'] = counts_by_state_as_string(DB::execute("SELECT state, COUNT(id) AS records_count FROM " . TABLE_PREFIX . 'notebook_pages GROUP BY state'));
  } // notebooks_handle_on_extra_stats