<?php

  /**
   * Application level attachments class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Attachments extends FwAttachments {

    /**
     * Find all project attachments
     *
     * @param Project $project
     * @param integer $min_state
     * @return DBResult
     */
    static function findForApiByProject(Project $project, $min_state = STATE_ARCHIVED) {
      $attachments_table = TABLE_PREFIX . 'attachments';

      if($project->getState() >= STATE_VISIBLE) {
        $map = Attachments::findTypeIdMapOfPotentialAttachmentParentsByProject($project, $min_state);

        if($map) {
          $conditions = array();

          foreach($map as $type => $ids) {
            $conditions[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
          } // if

          $conditions = implode(' OR ', $conditions);

          $result = DB::execute("SELECT id, type, parent_type, parent_id, state, name, mime_type, size FROM $attachments_table WHERE ($conditions) AND state >= ?", $min_state);

          if($result) {
            $result->setCasting(array(
              'id' => DBResult::CAST_INT,
              'parent_id' => DBResult::CAST_INT,
              'state' => DBResult::CAST_INT,
              'size' => DBResult::CAST_INT,
            ));

            return $result;
          } // if
        } // if
      } // if

      return null;
    } // findForApiByProject

    /**
     * Find all attachments in project and prepare them for export
     *
     * @param Project $project
     * @param array $parents_map
     * @param integer $changes_since
     * @return array
     */
    static function findForExport(Project $project, $parents_map, $changes_since) {
      $result = array();

      $attachments_table = TABLE_PREFIX . 'attachments';

      if(is_foreachable($parents_map)) {
        $conditions = array();

        foreach($parents_map as $type => $ids) {
          $conditions[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
        } // if

        $conditions = implode(' OR ', $conditions);

        $additional_condition = '';
        if(!is_null($changes_since)) {
          $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
          $additional_condition = "AND created_on > '$changes_since_date'";
        } // if

        $attachment = DB::execute("SELECT id, type, parent_type, parent_id, state, name, mime_type, size, location, md5, created_on, created_by_id, created_by_name, created_by_email FROM $attachments_table WHERE ($conditions) AND state >= ? $additional_condition", STATE_ARCHIVED);

        if($attachment instanceof DBResult) {
          $attachment->setCasting(array(
            'id' => DBResult::CAST_INT,
            'parent_id' => DBResult::CAST_INT,
            'state' => DBResult::CAST_INT,
            'size' => DBResult::CAST_INT,
            'created_by_id' => DBResult::CAST_INT
          ));

          foreach($attachment as $attachmen) {
            $result[] = array(
              'id'                => $attachmen['id'],
              'type'              => $attachmen['type'],
              'parent_type'       => $attachmen['parent_type'],
              'parent_id'         => $attachmen['parent_id'],
              'state'             => $attachmen['state'],
              'name'              => $attachmen['name'],
              'mime_type'         => $attachmen['mime_type'],
              'size'              => $attachmen['size'],
              'location'          => $attachmen['location'],
              'md5'               => $attachmen['md5'],
              'created_on'        => $attachmen['created_on'],
              'created_by_id'     => $attachmen['created_by_id'],
              'created_by_name'   => $attachmen['created_by_name'],
              'created_by_email'  => $attachmen['created_by_email']
            );
          } // foreach
        } // if
      } // if

      return $result;
    } // findForExport

    /**
     * Find all attachments in project and prepare them for export
     *
     * @param Project $project
     * @param string $output_file
     * @param array $parents_map
     * @param integer $changes_since
     * @return array
     */
    static function exportToFileByProject(Project $project, $output_file, &$parents_map, $changes_since) {
      if(!($output_handle = fopen($output_file, 'w+'))) {
        throw new Error(lang('Failed to write JSON file to :file_path', array('file_path' => $output_file)));
      } // if

      // Open json array
      fwrite($output_handle, '[');

      $attachments_table = TABLE_PREFIX . 'attachments';

      $count = 0;
      if(is_foreachable($parents_map)) {
        $conditions = array();

        foreach($parents_map as $type => $ids) {
          $conditions[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
        } // if

        $conditions = implode(' OR ', $conditions);

        $additional_condition = '';
        if(!is_null($changes_since)) {
          $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
          $additional_condition = "AND created_on > '$changes_since_date'";
        } // if

        $attachment = DB::execute("SELECT id, type, parent_type, parent_id, state, name, mime_type, size, location, md5, created_on, created_by_id, created_by_name, created_by_email FROM $attachments_table WHERE ($conditions) AND state >= ? $additional_condition", (boolean) $additional_condition ? STATE_TRASHED : STATE_ARCHIVED);

        if($attachment instanceof DBResult) {
          $attachment->setCasting(array(
            'id' => DBResult::CAST_INT,
            'parent_id' => DBResult::CAST_INT,
            'state' => DBResult::CAST_INT,
            'size' => DBResult::CAST_INT,
            'created_by_id' => DBResult::CAST_INT
          ));

          $buffer = '';
          foreach($attachment as $attachmen) {
            if($count > 0) $buffer .= ',';

            $buffer .= JSON::encode(array(
              'id'                => $attachmen['id'],
              'type'              => $attachmen['type'],
              'parent_type'       => $attachmen['parent_type'],
              'parent_id'         => $attachmen['parent_id'],
              'state'             => $attachmen['state'],
              'name'              => $attachmen['name'],
              'mime_type'         => $attachmen['mime_type'],
              'size'              => $attachmen['size'],
              'location'          => $attachmen['location'],
              'md5'               => $attachmen['md5'],
              'created_on'        => $attachmen['created_on'],
              'created_by_id'     => $attachmen['created_by_id'],
              'created_by_name'   => $attachmen['created_by_name'],
              'created_by_email'  => $attachmen['created_by_email']
            ));

            if($count % 15 == 0 && $count > 0) {
              fwrite($output_handle, $buffer);
              $buffer = '';
            } // if

            $parents_map[$attachmen['type']][] = $attachmen;
            $count++;
          } // foreach

          if($buffer) {
            fwrite($output_handle, $buffer);
          } // if
        } // if
      } // if

      // Close json array
      fwrite($output_handle, ']');

      // Close the handle and set correct permissions
      fclose($output_handle);
      @chmod($output_file, 0777);

      return $count;
    } // exportToFileByProject

    /**
     * Find type ID map of potential attechment parents in a given project
     *
     * @param Project $project
     * @param integer $min_state
     * @return array
     */
    static function findTypeIdMapOfPotentialAttachmentParentsByProject(Project $project, $min_state = STATE_ARCHIVED) {
      $map = array();

      $rows = DB::execute('SELECT id, type FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND state >= ?', $project->getId(), $min_state);
      if($rows) {
        foreach($rows as $row) {
          if(isset($map[$row['type']])) {
            $map[$row['type']][] = (integer) $row['id'];
          } else {
            $map[$row['type']] = array((integer) $row['id']);
          } // if
        } // foreach
      } // if

      EventsManager::trigger('on_extend_project_items_type_id_map', array(&$project, $min_state, &$map));

      $comment_parent_conditions = array();
      foreach($map as $type => $ids) {
        $comment_parent_conditions[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
      } // foreach

      if(count($comment_parent_conditions)) {
        $comment_parent_conditions = implode(' OR ', $comment_parent_conditions);

        $rows = DB::execute('SELECT id, type FROM ' . TABLE_PREFIX . "comments WHERE ($comment_parent_conditions) AND state >= ?", $min_state);
        if($rows) {
          $rows->setCasting(array(
            'id' => DBResult::CAST_INT,
          ));

          foreach($rows as $row) {
            if(array_key_exists($row['type'], $map)) {
              $map[$row['type']][] = $row['id'];
            } else {
              $map[$row['type']] = array($row['id']);
            } // if
          } // foreach
        } // if
      } // if

      return count($map) ? $map : null;
    } // findTypeIdMapOfPotentialAttachmentParentsByProjects

  }