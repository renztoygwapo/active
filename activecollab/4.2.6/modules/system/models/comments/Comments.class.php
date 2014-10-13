<?php

  /**
   * Comments manager class
   *
   * @package activeCollab.modules.resources
   * @subpackage models
   */
  class Comments extends FwComments {

    /**
     * Find all project attachments
     *
     * @param Project $project
     * @param integer $min_state
     * @return DBResult
     */
    static function findForApiByProject(Project $project, $min_state = STATE_ARCHIVED) {
      $comments_table = TABLE_PREFIX . 'comments';

      if($project->getState() >= STATE_VISIBLE) {
        $map = Comments::findTypeIdMapOfPotentialParents($project, $min_state);

        if($map) {
          $conditions = array();

          foreach($map as $type => $ids) {
            $conditions[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
          } // if

          $conditions = implode(' OR ', $conditions);

          $result = DB::execute("SELECT id, type, parent_type, parent_id, body, body AS 'body_formatted', state, created_on, created_by_id, created_by_name, created_by_email FROM $comments_table WHERE ($conditions) AND state >= ?", $min_state);

          if($result) {
            $result->setCasting(array(
              'id' => DBResult::CAST_INT,
              'parent_id' => DBResult::CAST_INT,
              'state' => DBResult::CAST_INT,
              'body_formatted' => function($in) {
                return HTML::toRichText($in);
              },
              'created_by_id' => DBResult::CAST_INT,
            ));

            return $result;
          } // if
        } // if
      } // if

      return null;
    } // findForApiByProject

    /**
     * Find all comments in project and prepare them for export
     *
     * @param Project $project
     * @param array $parents_map
     * @param integer $changes_since
     * @return array
     */
    static function findForExport(Project $project, &$parents_map, $changes_since) {
      $result = array();

      $comments_table = TABLE_PREFIX . 'comments';

      if(is_foreachable($parents_map)) {
        $conditions = array();

        foreach($parents_map as $type => $ids) {
          $conditions[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
        } // if

        $conditions = implode(' OR ', $conditions);

        $additional_condition = '';
        if(!is_null($changes_since)) {
          $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
          $additional_condition = "AND (created_on > '$changes_since_date' OR updated_on > '$changes_since_date')";
        } // if

        $comments = DB::execute("SELECT id, type, parent_type, parent_id, body, body AS 'body_formatted', state, created_on, created_by_id, created_by_name, created_by_email FROM $comments_table WHERE ($conditions) AND state >= ? $additional_condition", STATE_ARCHIVED);

        if($comments instanceof DBResult) {
          $comments->setCasting(array(
            'id' => DBResult::CAST_INT,
            'parent_id' => DBResult::CAST_INT,
            'state' => DBResult::CAST_INT,
            'body_formatted' => function($in) {
              return HTML::toRichText($in);
            },
            'created_by_id' => DBResult::CAST_INT
          ));

          foreach($comments as $comment) {
            $result[] = array(
              'id'                => $comment['id'],
              'body'              => $comment['body'],
              'body_formatted'    => $comment['body_formatted'],
              'type'              => $comment['type'],
              'parent_type'       => $comment['parent_type'],
              'parent_id'         => $comment['parent_id'],
              'state'             => $comment['state'],
              'created_on'        => $comment['created_on'],
              'created_by_id'     => $comment['created_by_id'],
              'created_by_name'   => $comment['created_by_name'],
              'created_by_email'  => $comment['created_by_email']
            );

            $parents_map[$comment['type']][] = $comment['id'];
          } // foreach
        } // if
      } // if

      return $result;
    } // findForExport

    /**
     * Find all comments in project and prepare them for export
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

      $comments_table = TABLE_PREFIX . 'comments';

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
          $additional_condition = "AND (created_on > '$changes_since_date' OR updated_on > '$changes_since_date')";
        } // if

        $comments = DB::execute("SELECT id, type, parent_type, parent_id, body, body AS 'body_formatted', state, created_on, created_by_id, created_by_name, created_by_email FROM $comments_table WHERE ($conditions) AND state >= ? $additional_condition", (boolean) $additional_condition ? STATE_TRASHED : STATE_ARCHIVED);

        if($comments instanceof DBResult) {
          $comments->setCasting(array(
            'id' => DBResult::CAST_INT,
            'parent_id' => DBResult::CAST_INT,
            'state' => DBResult::CAST_INT,
            'body_formatted' => function($in) {
              return HTML::toRichText($in);
            },
            'created_by_id' => DBResult::CAST_INT
          ));

          $buffer = '';
          foreach($comments as $comment) {
            if($count > 0) $buffer .= ',';

            $buffer .= JSON::encode(array(
              'id'                => $comment['id'],
              'body'              => $comment['body'],
              'body_formatted'    => $comment['body_formatted'],
              'type'              => $comment['type'],
              'parent_type'       => $comment['parent_type'],
              'parent_id'         => $comment['parent_id'],
              'state'             => $comment['state'],
              'created_on'        => $comment['created_on'],
              'created_by_id'     => $comment['created_by_id'],
              'created_by_name'   => $comment['created_by_name'],
              'created_by_email'  => $comment['created_by_email']
            ));

            if($count % 15 == 0 && $count > 0) {
              fwrite($output_handle, $buffer);
              $buffer = '';
            } // if

            $parents_map[$comment['type']][] = $comment['id'];
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
     * Find type ID map of potential comment parents in a given project
     *
     * @param Project $project
     * @param integer $min_state
     * @return array
     */
    static function findTypeIdMapOfPotentialParents(Project $project, $min_state = STATE_ARCHIVED) {
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

      return count($map) ? $map : null;
    } // findTypeIdMapOfPotentialParents

  }