<?php

  /**
   * Handle on_extra_stats event
   *
   * @package activeCollab.modules.tracking
   * @subpackage handlers
   */

  /**
   * Populate extra stats
   *
   * @param array $stats
   */
  function tracking_handle_on_extra_stats(&$stats) {
    $extract = function($table_name) {
      return array(
        counts_by_state_as_string(DB::execute("SELECT state, COUNT(id) AS records_count FROM $table_name GROUP BY state")),
        DB::executeFirstCell("SELECT MAX(DATE(created_on)) FROM $table_name"),
      );
    };

    $stats['time'] = implode(',', $extract(TABLE_PREFIX . 'time_records'));
    $stats['expenses'] = implode(',', $extract(TABLE_PREFIX . 'expenses'));
    $stats['estimates'] = implode(',', array(
      (integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'estimates'),
      DB::executeFirstCell('SELECT MAX(DATE(created_on)) FROM ' . TABLE_PREFIX . 'estimates'),
    ));
  } // tracking_handle_on_extra_stats