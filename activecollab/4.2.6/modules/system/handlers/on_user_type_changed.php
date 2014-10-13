<?php

  /**
   * on_user_type_changed event handler implementation
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Handle on_user_type_changed event
   *
   * @param User $user
   * @param string $from_class
   * @param string $new_class
   * @param User $by
   */
  function system_handle_on_user_type_changed(User &$user, $from_class, $new_class, User $by) {
    if($new_class == 'Client') {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $subtasks_table = TABLE_PREFIX . 'subtasks';

      $rows = DB::execute("SELECT id, type FROM $project_objects_table WHERE state >= ? AND visibility < ?", STATE_TRASHED, VISIBILITY_NORMAL);
      if($rows) {
        $private_objects = array();

        foreach($rows as $row) {
          $type = $row['type'];

          if(isset($private_objects[$type])) {
            $private_objects[$type][] = (integer) $row['id'];
          } else {
            $private_objects[$type] = array((integer) $row['id']);
          } // if
        } // foreach

        $parent_type_filter = array();

        foreach($private_objects as $type => $ids) {
          $parent_type_filter[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
        } // foreach

        $parent_type_filter = '(' . implode(' OR ', $parent_type_filter) . ')';

        // Clear user subscriptions
        DB::execute('DELETE FROM ' . TABLE_PREFIX . "subscriptions WHERE user_id = ? AND $parent_type_filter", $user->getId());

        // Get tasks assigned to this user
        $private_task_ids = DB::executeFirstColumn("SELECT id FROM $project_objects_table WHERE type = 'Task' AND assignee_id = ? AND state >= ? AND visibility < ?", $user->getId(), STATE_TRASHED, STATE_VISIBLE);

        if($private_task_ids) {
          DB::execute("UPDATE $project_objects_table SET assignee_id = NULL WHERE id IN (?)", $private_task_ids);
          DB::execute('DELETE FROM ' . TABLE_PREFIX . "assignments WHERE parent_type = 'Task' AND parent_id IN (?)", $private_task_ids);
        } // if

        // Clean up subtasks
        $rows = DB::execute("SELECT id, type FROM $subtasks_table WHERE state >= ? AND $parent_type_filter", STATE_TRASHED);
        if($rows) {
          $subtask_ids = array();
          $subtasks = array();

          foreach($rows as $row) {
            $type = $row['type'];
            $id = (integer) $row['id'];

            if(isset($subtasks[$type])) {
              $subtasks[$type][] = $id;
            } else {
              $subtasks[$type] = array($id);
            } // if

            $subtask_ids[] = $id;
          } // foreach

          $subtask_parent_type_filter = array();

          foreach($subtasks as $type => $ids) {
            $subtask_parent_type_filter[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
          } // foreach

          $subtask_parent_type_filter = '(' . implode(' OR ', $subtask_parent_type_filter) . ')';

          DB::execute('DELETE FROM ' . TABLE_PREFIX . "subscriptions WHERE user_id = ? AND $subtask_parent_type_filter", $user->getId());
          DB::execute("UPDATE $subtasks_table SET assignee_id = NULL WHERE id IN (?)", $subtask_ids);
        } // if
      } // if
    } // if
  } // system_handle_on_user_type_changed