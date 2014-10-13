<?php

  /**
   * Morning paper
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  final class MorningPaper {

    // Event categories
    const PREV = 'prev_business_day';
    const TODAY = 'today';

    // Event types
    const PROJECT_COMPLETED = 'project_completed';
    const MILESTONE_COMPLETED = 'milestone_completed';
    const TASK_COMPLETED = 'task_completed';
    const SUBTASK_COMPLETED = 'subtask_completed';
    const PROJECT_STARTED = 'project_started';
    const DISCUSSION_STARTED = 'discussion_started';
    const FILE_UPLOADED = 'file_uploaded';

    const MILESTONE_DUE = 'milestone_due';
    const TASK_DUE = 'task_due';
    const SUBTASK_DUE = 'subtask_due';

    /**
     * Send given day data to the users
     *
     * @param DateValue $day
     */
    static function send(DateValue $day) {
      if($day->isWorkday() && !$day->isDayOff()) {
        $users = Users::findBySQL('SELECT * FROM ' . TABLE_PREFIX . 'users WHERE state = ? AND type IN (?)', STATE_VISIBLE, self::whoCanReceiveMorningPaper());

        if($users) {
          $snapshot = self::getSnapshot($day, true);

          foreach($users as $user) {
            if(ConfigOptions::getValueFor('morning_paper_enabled', $user)) {
              list($prev_data, $today_data) = $snapshot->getDataFor($user, $user->isProjectManager() && ConfigOptions::getValueFor('morning_paper_include_all_projects', $user));

              if($prev_data || $today_data) {
                $first_morning_paper = ConfigOptions::getValueFor('first_morning_paper', $user);

                AngieApplication::notifications()
                  ->notifyAbout('system/morning_paper')
                  ->setPaperDay($day)
                  ->setPreviousDay($snapshot->getPreviousDay())
                  ->setPaperData($prev_data, $today_data, $first_morning_paper)
                  ->sendToUsers(array($user));

                if($first_morning_paper) {
                  ConfigOptions::setValueFor('first_morning_paper', $user, false);
                } // if
              } // if
            } // if
          } // foreach
        } // if
      } // if

      ConfigOptions::setValue('morning_paper_last_activity', time());
    } // send

    /**
     * Return snapshot for a given day
     *
     * @param DateValue $day
     * @return MorningPaperSnapshot
     * @throws InvalidParamError
     */
    static function getSnapshot(DateValue $day) {
      if($day->isWorkday() && !$day->isDayOff()) {
        return self::createDaySnapshot($day);
      } else {
        throw new InvalidParamError('day', $day, 'Day should be a work day');
      } // if
    } // getSnapshot

    /**
     * Return day snapshot
     *
     * @param DateValue $day
     * @return MorningPaperSnapshot
     */
    static private function createDaySnapshot(DateValue $day) {
      $pre_boundaries = self::getPreviousBusinessDayBoundaries($day);
      $today_boundaries = self::getTodayBoundaries($day);

      $snapshot_data = array(
        self::PREV => array(
          'date' => $pre_boundaries[2]->toMySQL(),
          'boundaries' => array(
            'from' => $pre_boundaries[0]->toMySQL(),
            'to' => $pre_boundaries[1]->toMySQL(),
          ),
          'events' => array(),
        ),
        self::TODAY => array(
          'boundaries' => array(
            'from' => $today_boundaries[0]->toMySQL(),
            'to' => $today_boundaries[1]->toMySQL(),
          ),
          'events' => array(),
        ),
      );

      $id_details_map = Projects::getIdDetailsMap(array('name', 'slug'), null, DB::prepare('completed_on IS NULL OR completed_on >= ?', $pre_boundaries[0]));

      if($id_details_map && is_foreachable($id_details_map)) {
        self::queryUsers();
        self::queryProjects($snapshot_data, $pre_boundaries[0], $pre_boundaries[1], $id_details_map);
        self::queryProjectObjects($snapshot_data, $day, $pre_boundaries[0], $pre_boundaries[1], $id_details_map);
        self::querySubtasks($snapshot_data, $day, $pre_boundaries[0], $pre_boundaries[1], $id_details_map);
      } // if

      return new MorningPaperSnapshot($snapshot_data);
    } // createDaySnapshot

    /**
     * Array of user names
     *
     * @var array
     */
    static private $user_names = array();

    /**
     * Query user info
     */
    static private function queryUsers() {
      foreach(DB::execute('SELECT id, first_name, last_name, email FROM ' . TABLE_PREFIX . 'users WHERE state = ?', STATE_VISIBLE) as $row) {
        self::$user_names[(integer) $row['id']] = Users::getUserDisplayName($row, true);
      } // foreach
    } // queryUsers

    /**
     * Qeury project data (completed and started)
     *
     * @param array $snapshot_data
     * @param DateTimeValue $from
     * @param DateTimeValue $to
     * @param array $id_details_map
     */
    static private function queryProjects(&$snapshot_data, DateTimeValue $from, DateTimeValue $to, $id_details_map) {
      $project_ids = array_keys($id_details_map);

      // Lets get started projects
      $rows = DB::execute('SELECT id, name, created_on, created_by_id AS "action_by_id", created_by_name AS "action_by_name", created_by_email AS "action_by_email" FROM ' . TABLE_PREFIX . 'projects WHERE id IN (?) AND state >= ? AND created_on BETWEEN ? AND ?', $project_ids, STATE_ARCHIVED, $from, $to);
      if($rows) {
        $rows->setCasting(array(
          'id' => DBResult::CAST_INT,
          'created_on' => DBResult::CAST_DATETIME,
          'action_by_id' => DBResult::CAST_INT,
        ));

        foreach($rows as $row) {
          $project_id = $row['id'];

          MorningPaper::logProjectEvent($snapshot_data, MorningPaper::PROJECT_STARTED, $row['created_on'], array(
            'id' => $project_id,
            'project_id' => $project_id,
            'project_name' => $id_details_map[$project_id] ? $id_details_map[$project_id]['name'] : '--',
            'name' => $row['name'],
            'permalink' => self::getProjectUrl(isset($id_details_map[$project_id]) && $id_details_map[$project_id]['slug'] ? $id_details_map[$project_id]['slug'] : $project_id),
          ), $row['action_by_id'], $row['action_by_name'], $row['action_by_email']);
        } // foreach
      } // if

      // Lets get completed projects
      $rows = DB::execute('SELECT id, name, completed_on, completed_by_id AS "action_by_id", completed_by_name AS "action_by_name", completed_by_email AS "action_by_email" FROM ' . TABLE_PREFIX . 'projects WHERE id IN (?) AND state >= ? AND completed_on BETWEEN ? AND ?', $project_ids, STATE_ARCHIVED, $from, $to);
      if($rows) {
        $rows->setCasting(array(
          'id' => DBResult::CAST_INT,
          'completed_on' => DBResult::CAST_DATETIME,
          'action_by_id' => DBResult::CAST_INT,
        ));

        foreach($rows as $row) {
          $project_id = $row['id'];

          MorningPaper::logProjectEvent($snapshot_data, MorningPaper::PROJECT_COMPLETED, $row['completed_on'], array(
            'id' => $project_id,
            'project_id' => $project_id,
            'project_name' => $id_details_map[$project_id] ? $id_details_map[$project_id]['name'] : '--',
            'name' => $row['name'],
            'permalink' => self::getProjectUrl(isset($id_details_map[$project_id]) && $id_details_map[$project_id]['slug'] ? $id_details_map[$project_id]['slug'] : $project_id),
          ), $row['action_by_id'], $row['action_by_name'], $row['action_by_email']);
        } // foreach
      } // if
    } // queryProjects

    /**
     * Query tasks and milestone changes
     *
     * @param array $snapshot_data
     * @param DateValue $day
     * @param DateTimeValue $from
     * @param DateTimeValue $to
     * @param array $id_details_map
     */
    static private function queryProjectObjects(&$snapshot_data, DateValue $day, DateTimeValue $from, DateTimeValue $to, $id_details_map) {
      $projects_table = TABLE_PREFIX . 'projects';
      $project_objects_table = TABLE_PREFIX . 'project_objects';

      // Lets get tasks that are completed previous business day
      $rows = DB::execute("SELECT id, type, project_id, name, completed_on, integer_field_1 AS 'task_id', completed_by_id AS 'action_by_id', completed_by_name AS 'action_by_name', completed_by_email AS 'action_by_email' FROM $project_objects_table WHERE project_id IN (?) AND type IN (?) AND state >= ? AND completed_on BETWEEN ? AND ?", array_keys($id_details_map), array('Milestone', 'Task'), STATE_ARCHIVED, $from, $to);
      if($rows) {
        $rows->setCasting(array(
          'id' => DBResult::CAST_INT,
          'project_id' => DBResult::CAST_INT,
          'completed_on' => DBResult::CAST_DATETIME,
          'task_id' => DBResult::CAST_INT,
        ));

        foreach($rows as $row) {
          $project_id = $row['project_id'];
          $project_slug = isset($id_details_map[$project_id]) && $id_details_map[$project_id]['slug'] ? $id_details_map[$project_id]['slug'] : $project_id;

          if($row['type'] == 'Task') {
            MorningPaper::logProjectEvent($snapshot_data, MorningPaper::TASK_COMPLETED, $row['completed_on'], array(
              'id' => $row['id'],
              'project_id' => $project_id,
              'project_name' => $id_details_map[$project_id] ? $id_details_map[$project_id]['name'] : '--',
              'name' => $row['name'],
              'task_id' => $row['task_id'],
              'permalink' => self::getTaskUrl($project_slug, $row['task_id']),
            ), $row['action_by_id'], $row['action_by_name'], $row['action_by_email']);
          } elseif($row['type'] == 'Milestone') {
            MorningPaper::logProjectEvent($snapshot_data, MorningPaper::MILESTONE_COMPLETED, $row['completed_on'], array(
              'id' => $row['id'],
              'project_id' => $project_id,
              'project_name' => $id_details_map[$project_id] ? $id_details_map[$project_id]['name'] : '--',
              'name' => $row['name'],
              'permalink' => self::getMilestoneUrl($project_slug, $row['id']),
            ), $row['action_by_id'], $row['action_by_name'], $row['action_by_email']);
          } // if
        } // foreach
      } // if

      // Lets get files and discussions that were started / uploaded previous business day
      $rows = DB::execute("SELECT id, type, project_id, name, created_on, created_by_id AS 'action_by_id', created_by_name AS 'action_by_name', created_by_email AS 'action_by_email' FROM $project_objects_table WHERE project_id IN (?) AND type IN (?) AND state >= ? AND created_on BETWEEN ? AND ?", array_keys($id_details_map), array('Discussion', 'File'), STATE_ARCHIVED, $from, $to);
      if($rows) {
        $rows->setCasting(array(
          'id' => DBResult::CAST_INT,
          'project_id' => DBResult::CAST_INT,
          'created_on' => DBResult::CAST_DATETIME,
        ));

        foreach($rows as $row) {
          $project_id = $row['project_id'];
          $project_slug = isset($id_details_map[$project_id]) && $id_details_map[$project_id]['slug'] ? $id_details_map[$project_id]['slug'] : $project_id;

          if($row['type'] == 'File') {
            MorningPaper::logProjectEvent($snapshot_data, MorningPaper::FILE_UPLOADED, $row['created_on'], array(
              'id' => $row['id'],
              'project_id' => $project_id,
              'project_name' => $id_details_map[$project_id] ? $id_details_map[$project_id]['name'] : '--',
              'name' => $row['name'],
              'permalink' => self::getFileUrl($project_slug, $row['id']),
            ), $row['action_by_id'], $row['action_by_name'], $row['action_by_email']);
          } elseif($row['type'] == 'Discussion') {
            MorningPaper::logProjectEvent($snapshot_data, MorningPaper::DISCUSSION_STARTED, $row['created_on'], array(
              'id' => $row['id'],
              'project_id' => $project_id,
              'project_name' => $id_details_map[$project_id] ? $id_details_map[$project_id]['name'] : '--',
              'name' => $row['name'],
              'permalink' => self::getDiscussionUrl($project_slug, $row['id']),
            ), $row['action_by_id'], $row['action_by_name'], $row['action_by_email']);
          } // if
        } // foreach
      } // if

      // Get milestone due today or late, and also query date_diff so we can know how many days it is late or whether it is due today
      $rows = DB::execute("SELECT $project_objects_table.id, $project_objects_table.type, $project_objects_table.project_id, $project_objects_table.name, $project_objects_table.due_on, $project_objects_table.assignee_id, $project_objects_table.integer_field_1 AS 'task_id', DATEDIFF($project_objects_table.due_on, ?) AS 'diff' FROM $projects_table, $project_objects_table WHERE ($projects_table.id = $project_objects_table.project_id AND $projects_table.completed_on IS NULL) AND $project_objects_table.type = 'Milestone' AND $project_objects_table.state >= ? AND $project_objects_table.completed_on IS NULL AND ($project_objects_table.due_on IS NOT NULL AND $project_objects_table.due_on <= ?)", $day, STATE_ARCHIVED, $day);
      if($rows) {
        $rows->setCasting(array(
          'id' => DBResult::CAST_INT,
          'project_id' => DBResult::CAST_INT,
          'due_on' => DBResult::CAST_DATE,
          'assignee_id' => DBResult::CAST_INT,
          'task_id' => DBResult::CAST_INT,
          'diff' => DBResult::CAST_INT,
        ));

        foreach($rows as $row) {
          $project_id = $row['project_id'];
          $project_slug = isset($id_details_map[$project_id]) && $id_details_map[$project_id]['slug'] ? $id_details_map[$project_id]['slug'] : $project_id;

          MorningPaper::logProjectEvent($snapshot_data, MorningPaper::MILESTONE_DUE, $row['due_on'], array(
            'id' => $row['id'],
            'project_id' => $project_id,
            'project_name' => $id_details_map[$project_id] ? $id_details_map[$project_id]['name'] : '--',
            'name' => $row['name'],
            'permalink' => self::getMilestoneUrl($project_slug, $row['id']),
            'assignee_id' => $row['assignee_id'],
            'diff' => $row['diff'],
          ));
        } // foreach
      } // if

      // Get tasks due today or late, that have assignee_id set, and also query date_diff so we can know how many days it is late or whether it is due today
      $rows = DB::execute("SELECT $project_objects_table.id, $project_objects_table.type, $project_objects_table.project_id, $project_objects_table.name, $project_objects_table.due_on, $project_objects_table.assignee_id, $project_objects_table.integer_field_1 AS 'task_id', DATEDIFF($project_objects_table.due_on, ?) AS 'diff' FROM $projects_table, $project_objects_table WHERE ($projects_table.id = $project_objects_table.project_id AND $projects_table.completed_on IS NULL) AND $project_objects_table.type = 'Task' AND $project_objects_table.state >= ? AND $project_objects_table.completed_on IS NULL AND ($project_objects_table.assignee_id IS NOT NULL AND $project_objects_table.assignee_id > 0) AND ($project_objects_table.due_on IS NOT NULL AND $project_objects_table.due_on <= ?)", $day, STATE_ARCHIVED, $day);
      if($rows) {
        $rows->setCasting(array(
          'id' => DBResult::CAST_INT,
          'project_id' => DBResult::CAST_INT,
          'due_on' => DBResult::CAST_DATE,
          'assignee_id' => DBResult::CAST_INT,
          'task_id' => DBResult::CAST_INT,
          'diff' => DBResult::CAST_INT,
        ));

        foreach($rows as $row) {
          $project_id = $row['project_id'];
          $project_slug = isset($id_details_map[$project_id]) && $id_details_map[$project_id]['slug'] ? $id_details_map[$project_id]['slug'] : $project_id;

          MorningPaper::logProjectEvent($snapshot_data, MorningPaper::TASK_DUE, $row['due_on'], array(
            'id' => $row['id'],
            'project_id' => $project_id,
            'project_name' => $id_details_map[$project_id] ? $id_details_map[$project_id]['name'] : '--',
            'name' => $row['name'],
            'task_id' => $row['task_id'],
            'permalink' => self::getTaskUrl($project_slug, $row['task_id']),
            'assignee_id' => $row['assignee_id'],
            'diff' => $row['diff'],
          ));
        } // foreach
      } // if
    } // queryProjectObjects

    /**
     * Query subtask details
     *
     * @param array $snapshot_data
     * @param DateValue $day
     * @param DateTimeValue $from
     * @param DateTimeValue $to
     * @param array $id_details_map
     */
    static private function querySubtasks(&$snapshot_data, DateValue $day, DateTimeValue $from, DateTimeValue $to, $id_details_map) {
      $tasks_table = TABLE_PREFIX . 'project_objects';
      $subtasks_table = TABLE_PREFIX . 'subtasks';

      $rows = DB::execute("SELECT id, name, project_id, assignee_id, completed_on, integer_field_1 AS 'task_id' FROM $tasks_table WHERE project_id IN (?) AND type = 'Task' AND state >= ?", array_keys($id_details_map), STATE_ARCHIVED);
      if($rows) {
        $rows->setCasting(array(
          'id' => DBResult::CAST_INT,
          'project_id' => DBResult::CAST_INT,
          'assignee_id' => DBResult::CAST_INT,
          'completed_on' => DBResult::CAST_DATETIME,
          'task_id' => DBResult::CAST_INT,
        ));

        $tasks = array();

        foreach($rows as $row) {
          $tasks[$row['id']] = array(
            'project_id' => $row['project_id'],
            'name' => $row['name'],
            'assignee_id' => $row['assignee_id'],
            'is_completed' => $row['completed_on'] instanceof DateTimeValue,
            'task_id' => $row['task_id'],
          );
        } // foreach

        // Query completed subtasks
        $rows = DB::execute("SELECT id, parent_id, body, completed_on, completed_by_id AS 'action_by_id', completed_by_name AS 'action_by_name', completed_by_email AS 'action_by_email' FROM $subtasks_table WHERE parent_type = 'Task' AND parent_id IN (?) AND state >= ? AND completed_on BETWEEN ? AND ?", array_keys($tasks), STATE_ARCHIVED, $from, $to);
        if($rows) {
          $rows->setCasting(array(
            'id' => DBResult::CAST_INT,
            'parent_id' => DBResult::CAST_INT,
            'completed_on' => DBResult::CAST_DATETIME,
          ));

          foreach($rows as $row) {
            $parent_id = $row['parent_id'];

            if(isset($tasks[$parent_id])) {
              $project_id = $tasks[$parent_id]['project_id'];
            } else {
              continue;
            } // if

            $project_slug = isset($id_details_map[$project_id]) && $id_details_map[$project_id]['slug'] ? $id_details_map[$project_id]['slug'] : $project_id;

            MorningPaper::logProjectEvent($snapshot_data, MorningPaper::SUBTASK_COMPLETED, $row['completed_on'], array(
              'id' => $row['id'],
              'project_id' => $project_id,
              'project_name' => $id_details_map[$project_id] ? $id_details_map[$project_id]['name'] : '--',
              'name' => $row['body'],
              'permalink' => self::getSubtaskUrl($project_slug, $tasks[$parent_id]['task_id'], $row['id']),
              'task_id' => $tasks[$parent_id]['task_id'],
              'task_name' => $tasks[$parent_id]['name'],
              'task_assignee_id' => $tasks[$parent_id]['assignee_id'],
              'task_is_completed' => $tasks[$parent_id]['is_completed'],
              'task_permalink' => self::getTaskUrl($project_slug, $tasks[$parent_id]['task_id']),
            ), $row['action_by_id'], $row['action_by_name'], $row['action_by_email']);
          } // foreach
        } // if

        // Query subtasks that are due today or late
        $rows = DB::execute("SELECT id, parent_id, body, due_on, assignee_id, DATEDIFF(due_on, ?) AS 'diff' FROM $subtasks_table WHERE parent_type = 'Task' AND parent_id IN (?) AND state >= ? AND completed_on IS NULL AND assignee_id IS NOT NULL AND assignee_id > 0 AND due_on IS NOT NULL AND due_on <= ?", $day, array_keys($tasks), STATE_ARCHIVED, $day);
        if($rows) {
          $rows->setCasting(array(
            'id' => DBResult::CAST_INT,
            'parent_id' => DBResult::CAST_INT,
            'due_on' => DBResult::CAST_DATE,
            'assignee_id' => DBResult::CAST_INT,
            'diff' => DBResult::CAST_INT,
          ));

          foreach($rows as $row) {
            $parent_id = $row['parent_id'];

            if(isset($tasks[$parent_id])) {
              $project_id = $tasks[$parent_id]['project_id'];
            } else {
              continue;
            } // if

            $project_slug = isset($id_details_map[$project_id]) && $id_details_map[$project_id]['slug'] ? $id_details_map[$project_id]['slug'] : $project_id;

            MorningPaper::logProjectEvent($snapshot_data, MorningPaper::SUBTASK_DUE, $row['due_on'], array(
              'id' => $row['id'],
              'project_id' => $project_id,
              'project_name' => $id_details_map[$project_id] ? $id_details_map[$project_id]['name'] : '--',
              'name' => $row['body'],
              'permalink' => self::getSubtaskUrl($project_slug, $tasks[$parent_id]['task_id'], $row['id']),
              'task_id' => $tasks[$parent_id]['task_id'],
              'task_name' => $tasks[$parent_id]['name'],
              'task_permalink' => self::getTaskUrl($project_slug, $tasks[$parent_id]['task_id']),
              'assignee_id' => $row['assignee_id'],
              'diff' => $row['diff'],
            ));
          } // foreach
        } // if
      } // if
    } // querySubtasks

    /**
     * Task URL pattern
     *
     * @var string
     */
    static private $project_url_pattern;

    /**
     * Return project URL
     *
     * @param string|integer $project_slug
     * @return string
     */
    static private function getProjectUrl($project_slug) {
      if(empty(self::$project_url_pattern)) {
        self::$project_url_pattern = Router::assemble('project', array('project_slug' => '--PROJECT-SLUG--'));
      } // if

      return str_replace('--PROJECT-SLUG--', $project_slug, self::$project_url_pattern);
    } // getTaskUrl

    /**
     * Task URL pattern
     *
     * @var string
     */
    static private $milestone_url_pattern;

    /**
     * Return milestone URL
     *
     * @param string|integer $project_slug
     * @param integer $milestone_id
     * @return string
     */
    static private function getMilestoneUrl($project_slug, $milestone_id) {
      if(empty(self::$milestone_url_pattern)) {
        self::$milestone_url_pattern = Router::assemble('project_milestone', array(
          'project_slug' => '--PROJECT-SLUG--',
          'milestone_id' => '--MILESTONE-ID--',
        ));
      } // if

      return str_replace(array('--PROJECT-SLUG--', '--MILESTONE-ID--'), array($project_slug, $milestone_id), self::$milestone_url_pattern);
    } // getMilestoneUrl

    /**
     * Discussion URL pattern
     *
     * @var string
     */
    static private $discussion_url_pattern;

    /**
     * Return discussion URL
     *
     * @param string|integer $project_slug
     * @param integer $discussion_id
     * @return string
     */
    static private function getDiscussionUrl($project_slug, $discussion_id) {
      if(empty(self::$discussion_url_pattern)) {
        self::$discussion_url_pattern = Router::assemble('project_discussion', array(
          'project_slug' => '--PROJECT-SLUG--',
          'discussion_id' => '--DISCUSSION-ID--',
        ));
      } // if

      return str_replace(array('--PROJECT-SLUG--', '--DISCUSSION-ID--'), array($project_slug, $discussion_id), self::$discussion_url_pattern);
    } // getDiscussionUrl

    /**
     * Discussion URL pattern
     *
     * @var string
     */
    static private $file_url_pattern;

    /**
     * Return file URL
     *
     * @param string|integer $project_slug
     * @param integer $file_id
     * @return string
     */
    static private function getFileUrl($project_slug, $file_id) {
      if(empty(self::$file_url_pattern)) {
        self::$file_url_pattern = Router::assemble('project_assets_file', array(
          'project_slug' => '--PROJECT-SLUG--',
          'asset_id' => '--FILE-ID--',
        ));
      } // if

      return str_replace(array('--PROJECT-SLUG--', '--FILE-ID--'), array($project_slug, $file_id), self::$file_url_pattern);
    } // getFileUrl

    /**
     * Task URL pattern
     *
     * @var string
     */
    static private $task_url_pattern;

    /**
     * Return task URL
     *
     * @param string|integer $project_slug
     * @param integer $task_id
     * @return string
     */
    static private function getTaskUrl($project_slug, $task_id) {
      if(empty(self::$task_url_pattern)) {
        self::$task_url_pattern = Router::assemble('project_task', array(
          'project_slug' => '--PROJECT-SLUG--',
          'task_id' => '--TASK-ID--',
        ));
      } // if

      return str_replace(array('--PROJECT-SLUG--', '--TASK-ID--'), array($project_slug, $task_id), self::$task_url_pattern);
    } // getTaskUrl

    /**
     * Subtask URL pattern
     *
     * @var string
     */
    static private $subtask_url_pattern;

    /**
     * Return subtask URL
     *
     * @param string|integer $project_slug
     * @param integer $task_id
     * @param integer $subtask_id
     * @return string
     */
    private function getSubtaskUrl($project_slug, $task_id, $subtask_id) {
      if(empty(self::$subtask_url_pattern)) {
        self::$subtask_url_pattern = Router::assemble('project_task', array(
          'project_slug' => '--PROJECT-SLUG--',
          'task_id' => '--TASK-ID--',
          'subtask_id' => '--SUBTASK-ID--',
        ));
      } // if

      return str_replace(array('--PROJECT-SLUG--', '--TASK-ID--', '--SUBTASK-ID--'), array($project_slug, $task_id, $subtask_id), self::$subtask_url_pattern);
    } // getSubtaskUrl

    /**
     * Log an event
     *
     * @param array $data
     * @param string $event
     * @param DateTimeValue $event_timestamp
     * @param array $event_details
     * @param integer|null $by_id
     * @param integer|null $by_name
     * @param integer|null $by_email
     * @throws InvalidParamError
     */
    static private function logProjectEvent(&$data, $event, $event_timestamp, $event_details, $by_id = null, $by_name = null, $by_email = null) {
      switch($event) {
        case self::PROJECT_COMPLETED:
        case self::MILESTONE_COMPLETED:
        case self::TASK_COMPLETED:
        case self::SUBTASK_COMPLETED:
        case self::PROJECT_STARTED:
        case self::FILE_UPLOADED:
        case self::DISCUSSION_STARTED:
          $where = MorningPaper::PREV;
          break;
        default:
          $where = MorningPaper::TODAY;
      } // if

      if(empty($event_details['project_id'])) {
        throw new InvalidParamError('event_details', $event_details, 'project_id is required');
      } // if

      $project_id = $event_details['project_id'];

      $event_details['action_by_id'] = $by_id; // Make sure that we have action by ID set

      if($by_id) {
        $event_details['action_by'] = isset(self::$user_names[$by_id]) && self::$user_names[$by_id] ? self::$user_names[$by_id] : Users::getUserDisplayName(array(
          'full_name' => $by_name,
          'email' => $by_email,
        ), true);
      } else {
        $event_details['action_by'] = null;
      } // if

      if(empty($data[$where]['events'][$project_id])) {
        $data[$where]['events'][$project_id] = array();
      } // if

      $data[$where]['events'][$project_id][] = array_merge(array('event' => $event, 'timestamp' => $event_timestamp->toMySQL()), $event_details);
    } // logProjectEvent

    /**
     * Return previous business day boundaries
     *
     * @param DateValue $day
     * @return DateTimeValue[]
     */
    static private function getPreviousBusinessDayBoundaries(DateValue $day) {
      $copy = clone($day);

      do {
        $copy->advance(-86400);
      } while(!$copy->isWorkday() || $copy->isDayOff());

      return array($copy->beginningOfDay(), $copy->endOfDay(), $copy);
    } // getPreviousBusinessDayBoundaries

    /**
     * Return today business day boundaries
     *
     * @param DateValue $day
     * @return DateTimeValue[]
     */
    static function getTodayBoundaries(DateValue $day) {
      return array($day->beginningOfDay(), $day->endOfDay());
    } // getTodayBoundaries

    /**
     * Return unsubscribe code for $user
     *
     * @param User $user
     * @return string
     */
    static function getSubscriptionCode(User $user) {
      $code = $user->getAdditionalProperty('subscription_code');

      if(empty($code) || strlen($code) != 10) {
        $code = strtoupper(make_string(10));

        $user->setAdditionalProperty('subscription_code', $code);
        $user->save();
      } // if

      return 'MRNGPPR-' . $user->getId() . '-' . $code;
    } // getSubscriptionCode

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Return true if $user can receive morning paper
     *
     * @param User $user
     * @return bool
     */
    static function canReceiveMorningPaper(User $user) {
      return !($user instanceof Client);
    } // canReceiveMorningPaper

    /**
     * Return list of roles that can use morning paper feature
     *
     * @return array
     */
    static function whoCanReceiveMorningPaper() {
      return array('Administrator', 'Manager', 'Member', 'Subcontractor');
    } // whoCanReceiveMorningPaper

  }