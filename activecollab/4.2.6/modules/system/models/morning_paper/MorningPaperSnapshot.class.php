<?php

  /**
   * Morning paper snapshot
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class MorningPaperSnapshot {

    /**
     * Parsed snapshot data
     *
     * @var array
     */
    private $data;

    /**
     * Create a new snapshot instance
     *
     * @param string|array $data
     * @throws InvalidParamError
     */
    function __construct($data) {
      if(is_string($data)) {
        $this->data = json_decode(file_get_contents($data), true);
      } elseif(is_array($data)) {
        $this->data = $data;
      } else {
        throw new InvalidParamError('data', $data, 'Snapshot data missing');
      } // if
    } // __construct

    /**
     * Return previous day timestamp
     *
     * @return string
     */
    function getPreviousDay() {
      return $this->data['prev_business_day']['date'];
    } // getPreviousDay

    /**
     * Return unfiltered snapshot data
     *
     * @return array
     */
    function getData() {
      return $this->data;
    } // getData

    /**
     * Return data for a given user
     *
     * @param User $user
     * @param bool $include_all
     * @return array
     */
    function getDataFor(User $user, $include_all = false) {
      $prev_data = $today_data = array();

      list($project_ids, $project_permissions) = $this->getProjectIdsAndPermissionsFor($user, $include_all);

      if($project_ids) {
        $user_id = $user->getId();
        $project_names = array();

        if($rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'projects WHERE id IN (?)', $project_ids)) {
          foreach($rows as $row) {
            $project_names[(integer) $row['id']] = $row['name'];
          } // foreach
        } // if

        foreach($this->data['today']['events'] as $project_id => $project_events) {
          if(in_array($project_id, $project_ids)) {
            foreach($project_events as $project_event) {

              // Skip task and milestone events if we don't have permissions to see them
              if($project_permissions) {
                if(empty($project_permissions[$project_id]['can_access_tasks']) && ($project_event['event'] == MorningPaper::TASK_DUE || $project_event['event'] == MorningPaper::SUBTASK_DUE)) {
                  continue;
                } elseif(empty($project_permissions[$project_id]['can_access_milestones']) && $project_event['event'] == MorningPaper::MILESTONE_DUE) {
                  continue;
                } // if
              } // if

              if($project_event['event'] == MorningPaper::MILESTONE_DUE) {
                $today_data[] = $project_event;
              } elseif(in_array($project_event['event'], array(MorningPaper::TASK_DUE, MorningPaper::SUBTASK_DUE)) && $project_event['assignee_id'] == $user_id) {
                $today_data[] = $project_event;
              } // if
            } // foreach
          } // if
        } // foreach

        usort($today_data, function($a, $b) {
          return strcmp($b['timestamp'], $a['timestamp']);
        });

        foreach($this->data['prev_business_day']['events'] as $project_id => $project_events) {
          if(in_array($project_id, $project_ids)) {
            foreach($project_events as $project_event) {
              if(isset($project_event['action_by_id']) && $project_event['action_by_id'] == $user_id) {
                continue; // Skip actions performed by recipient
              } // if

              // Skip task and milestone events if we don't have permissions to see them
              if($project_permissions) {
                if(empty($project_permissions[$project_id]['can_access_tasks']) && ($project_event['event'] == MorningPaper::TASK_COMPLETED || $project_event['event'] == MorningPaper::SUBTASK_COMPLETED)) {
                  continue;
                } elseif(empty($project_permissions[$project_id]['can_access_milestones']) && $project_event['event'] == MorningPaper::MILESTONE_COMPLETED) {
                  continue;
                } elseif(empty($project_permissions[$project_id]['can_access_files']) && $project_event['event'] == MorningPaper::FILE_UPLOADED) {
                  continue;
                } elseif(empty($project_permissions[$project_id]['can_access_discussions']) && $project_event['event'] == MorningPaper::DISCUSSION_STARTED) {
                  continue;
                } // if
              } // if

              if($project_event['event'] == MorningPaper::SUBTASK_COMPLETED && $project_event['task_assignee_id'] != $user_id) {
                continue; // Skip subtask completion events for tasks that are not assigned to the recipient
              } // if

              if(empty($prev_data[$project_id])) {
                $prev_data[$project_id] = array(
                  'name' => isset($project_names[$project_id]) && $project_names[$project_id] ? $project_names[$project_id] : lang('Unknown'),
                  'events' => array(),
                );
              } // if

              $prev_data[$project_id]['events'][] = $project_event;
            } // foreach
          } // if
        } // foreach

        foreach($prev_data as $project_id => $project_data) {
          uasort($prev_data[$project_id]['events'], function($a, $b) {
            return strcmp($a['timestamp'], $b['timestamp']);
          });
        } // foreach

        uasort($prev_data, function($a, $b) {
          return strcmp($a['name'], $b['name']);
        });
      } // if

      if(empty($prev_data)) {
        $prev_data = null;
      } // if

      if(empty($today_data)) {
        $today_data = null;
      } // if

      return array($prev_data, $today_data);
    } // getDataFor

    /**
     * Return projects ID-s and project permissions for a given user
     *
     * @param User $user
     * @param boolean $include_all
     * @return array
     */
    function getProjectIdsAndPermissionsFor(User $user, $include_all) {
      if($user->isProjectManager()) {
        $project_permissions = null;

        $project_ids = Projects::findIdsByUser($user, $include_all, DB::prepare('state >= ?', STATE_VISIBLE));
      } else {
        $project_permissions = array();

        $projects_table = TABLE_PREFIX . 'projects';
        $project_users_table = TABLE_PREFIX . 'project_users';

        $rows = DB::execute("SELECT $project_users_table.project_id, $project_users_table.permissions, $project_users_table.role_id FROM $projects_table, $project_users_table WHERE $projects_table.id = $project_users_table.project_id AND $project_users_table.user_id = ? AND $projects_table.state >= ?", $user->getId(), STATE_VISIBLE);
        if($rows) {
          $rows->setCasting(array(
            'project_id' => DBResult::CAST_INT,
            'role_id' => DBResult::CAST_INT,
          ));

          foreach($rows as $row) {
            $project_permissions[$row['project_id']] = array(
              'can_access_tasks' => $this->getProjectPermission('task', $row),
              'can_access_milestones' => $this->getProjectPermission('milestone', $row),
              'can_access_files' => $this->getProjectPermission('file', $row),
              'can_access_discussions' => $this->getProjectPermission('discussion', $row),
            );
          } // foreach
        } // if

        if(count($project_permissions)) {
          $project_ids = array_keys($project_permissions);
        } else {
          $project_permissions = $project_ids = null;
        } // if
      } // if

      return array($project_ids, $project_permissions);
    } // getProjectIdsAndPermissionsFor

    /**
     * Cached project roles
     *
     * @var ProjectRole[]
     */
    protected $project_roles = false;

    /**
     * Return true if user has a particular permission in a given project (based on info from project_users table record)
     *
     * @param string $permission
     * @param array $project_users_record
     * @return boolean
     */
    private function getProjectPermission($permission, $project_users_record) {
      if($this->project_roles === false) {
        $this->project_roles = array();

        if(ProjectRoles::count()) {
          foreach(ProjectRoles::find() as $project_role) {
            $this->project_roles[$project_role->getId()] = $project_role;
          } // foreach
        } // if
      } // if

      if($project_users_record['role_id']) {
        $role_id = $project_users_record['role_id'];

        return isset($this->project_roles[$role_id]) && $this->project_roles[$role_id] instanceof ProjectRole ? $this->project_roles[$role_id]->getPermissionValue($permission) >= ProjectRole::PERMISSION_ACCESS : false;
      } else {
        $permissions = isset($project_users_record['permissions']) && $project_users_record['permissions'] ? unserialize($project_users_record['permissions']) : array();

        return is_array($permissions) && isset($permissions[$permission]) && $permissions[$permission] >= ProjectRole::PERMISSION_ACCESS;
      } // if
    } // getProjectPermission

    /**
     * Return today events date and time boundaries
     *
     * @return DateTimeValue[]
     */
    function getTodayBoundaries() {
      if(isset($this->data['today']) && isset($this->data['today']['boundaries']) && isset($this->data['today']['boundaries']['from']) && isset($this->data['today']['boundaries']['to'])) {
        return array(DateTimeValue::makeFromString($this->data['today']['boundaries']['from']), DateTimeValue::makeFromString($this->data['today']['boundaries']['to']));
      } else {
        return array(null, null);
      } // if
    } // getTodayBoundaries

    /**
     * Return previous business day date and time boundaries
     *
     * @return DateTimeValue[]
     */
    function getPreviousBusinessDayBoundaries() {
      if(isset($this->data['prev_business_day']) && isset($this->data['prev_business_day']['boundaries']) && isset($this->data['prev_business_day']['boundaries']['from']) && isset($this->data['prev_business_day']['boundaries']['to'])) {
        return array(DateTimeValue::makeFromString($this->data['prev_business_day']['boundaries']['from']), DateTimeValue::makeFromString($this->data['prev_business_day']['boundaries']['to']));
      } else {
        return array(null, null);
      } // if
    } // getPreviousBusinessDayBoundaries

  }