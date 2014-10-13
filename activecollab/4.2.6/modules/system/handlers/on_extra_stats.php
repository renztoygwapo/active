<?php

  /**
   * Handle on_extra_stats event
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Populate extra stats
   *
   * @param array $stats
   */
  function system_handle_on_extra_stats(&$stats) {
    $rows = DB::execute("SELECT state, LOWER(type) AS 'type', COUNT(id) AS 'records_count' FROM " . TABLE_PREFIX . 'project_objects GROUP BY state, type');
    if($rows) {
      $project_objects_by_type = array();

      foreach($rows as $row) {
        $type = $row['type'];

        if(isset($project_objects_by_type[$type])) {
          $project_objects_by_type[$type][] = array('state' => $row['state'], 'records_count' => $row['records_count']);
        } else {
          $project_objects_by_type[$type] = array(array('state' => $row['state'], 'records_count' => $row['records_count']));
        } // if
      } // foreach

      foreach($project_objects_by_type as $type => $counts) {
        $stats["po_{$type}"] = counts_by_state_as_string($counts);
      } // foreach
    } // if

    $stats['comments'] = counts_by_state_as_string(DB::execute("SELECT state, COUNT(id) AS records_count FROM " . TABLE_PREFIX . 'comments GROUP BY state'));
    $stats['subtasks'] = counts_by_state_as_string(DB::execute("SELECT state, COUNT(id) AS records_count FROM " . TABLE_PREFIX . 'subtasks GROUP BY state'));
    $stats['projects'] = counts_by_state_as_string(DB::execute("SELECT state, COUNT(id) AS records_count FROM " . TABLE_PREFIX . 'projects GROUP BY state'));
    $stats['companies'] = counts_by_state_as_string(DB::execute("SELECT state, COUNT(id) AS records_count FROM " . TABLE_PREFIX . 'companies GROUP BY state'));
    $stats['users'] = counts_by_state_as_string(DB::execute("SELECT state, COUNT(id) AS records_count FROM " . TABLE_PREFIX . 'users GROUP BY state'));

    $stats['ausers'] = Users::count(array('last_activity_on >= ?', DateValue::makeFromString('-7 days')));
    $stats['lua'] = DB::executeFirstCell('SELECT MAX(DATE(last_activity_on)) FROM ' . TABLE_PREFIX . 'users');

    $stats['sch_tasks'] = implode(',', array(
      ConfigOptions::getValue('last_frequently_activity'),
      ConfigOptions::getValue('last_hourly_activity'),
      ConfigOptions::getValue('last_daily_activity'),
    ));

    $stats['mailboxes'] = IncomingMailboxes::count(array('is_enabled > 0'));
    $stats['r2c'] = (integer) IncomingMailboxes::testReplyToComments(AngieApplication::mailer()->getDefaultSender()->getEmail());
  } // system_handle_on_extra_stats