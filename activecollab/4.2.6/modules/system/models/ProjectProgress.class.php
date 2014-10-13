<?php

  /**
   * Static class used to calculate and cache data used to display project and 
   * milestone progress
   * 
   * @package activeCollab.modules.system
   * @subpackage model
   */
  final class ProjectProgress {
    
    // Cache ID-s
    const TOTAL_TASKS = 0;
    const OPEN_TASKS = 1;
    
    /**
     * Return progress of a given project
     * 
     * $project can be instance of Project class of ID of project that we want 
     * to get progress of
     * 
     * Result is array where first element is total number of tasks and secon 
     * element is number of open tasks
     * 
     * @param Project $project
     * @return array
     */
    static function getProjectProgress($project) {
      $cached_value = ProjectProgress::getProjectProgressCache($project);
      
      return array($cached_value[self::TOTAL_TASKS], $cached_value[self::OPEN_TASKS]);
    } // getProjectProgress
    
    /**
     * Return progress of a given milestone
     * 
     * Result is array where first element is total number of tasks and secon 
     * element is number of open tasks
     * 
     * @param Milestone $milestone
     * @return array
     */
    static function getMilestoneProgress(Milestone $milestone) {
      if($milestone->getProject() instanceof Project) {
        $cached_value = ProjectProgress::getProjectProgressCache($milestone->getProject());
        
        if(isset($cached_value['milestone_data'][$milestone->getId()])) {
          return array($cached_value['milestone_data'][$milestone->getId()][self::TOTAL_TASKS], $cached_value['milestone_data'][$milestone->getId()][self::OPEN_TASKS]);
        } // if
      } // if
      
      return array(0, 0);
    } // getMilestoneProgress
    
    /**
     * Return progress of a given project object
     * 
     * @param ProjectObject|array $object
     * @return array
     * @throws InvalidParamError
     */
    static function getObjectProgress($object) {
      if($object instanceof ProjectObject) {
        $project = $object->getProject();
        
        if($project instanceof Project) {
          $project_id = $project->getId();
          $object_id = $object->getId();
        } else {
          return array(0, 0);
        } // if
      } elseif(is_array($object)) {
        $project_id = isset($object['project_id']) ? (integer) $object['project_id'] : null;
        $object_id = isset($object['object_id']) ? (integer) $object['object_id'] : null;
      } else {
        throw new InvalidParamError('object', $object, '$object should be an instance of ProjectObject class or an array that describes it');
      } // if
      
      if($project_id && $object_id) {
        $cached_value = ProjectProgress::getProjectProgressCache($project_id);
        
        if(isset($cached_value['object_data'][$object_id])) {
          return array(
            $cached_value['object_data'][$object_id][self::TOTAL_TASKS], 
            $cached_value['object_data'][$object_id][self::OPEN_TASKS]
          );
        } // if
      } // if
      
      return array(0, 0);
    } // getObjectProjects
    
    /**
     * Drop project progress data cache for given project
     * 
     * If $project is not a valid Project class instance, cache for all projects 
     * will be dropped
     * 
     * @param Project $project
     */
    static function dropProjectProgressCache($project = null) {
      $project_id = $project instanceof Project ? $project->getId() : (integer) $project;

      if($project_id) {
        AngieApplication::cache()->removeByObject(array('projects', $project_id), 'progress');
      } else {
        AngieApplication::cache()->removeByModel('projects');
      } // if

      AngieApplication::cache()->remove('projects_quick_progress');
    } // dropProjectProgressCache

    /**
     * Return project progress data
     *
     * @param Project|int $project
     * @return array
     */
    static private function getProjectProgressCache($project) {
      $project_id = $project instanceof Project ? $project->getId() : $project;

      return AngieApplication::cache()->getByObject(array('projects', $project_id), 'progress', function() use ($project_id) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';
        $subtasks_table = TABLE_PREFIX . 'subtasks';

        $milestone_ids = DB::executeFirstColumn("SELECT id FROM $project_objects_table WHERE project_id = ? AND type = ? AND state >= ?", $project_id, 'Milestone', STATE_ARCHIVED);

        $result = array(
          ProjectProgress::TOTAL_TASKS => 0,
          ProjectProgress::OPEN_TASKS => 0,
          'milestone_data' => array(),
          'object_data' => array(),
        );

        if($milestone_ids) {
          foreach($milestone_ids as $milestone_id) {
            $result['milestone_data'][$milestone_id] = array(
              ProjectProgress::TOTAL_TASKS => 0,
              ProjectProgress::OPEN_TASKS => 0,
            );
          } // foerach
        } // if

        $rows = DB::execute("SELECT id, UPPER(type) AS 'type', completed_on, milestone_id FROM $project_objects_table WHERE project_id = ? AND type IN (?) AND state >= ?", $project_id, array('Task', 'TodoList'), STATE_ARCHIVED);
        if($rows) {
          $project_object_milestones_map = array();
          $subtask_parent_ids = array();

          // Lets go through project objects
          foreach($rows as $row) {

            // Count project objects
            $result[ProjectProgress::TOTAL_TASKS] += 1;
            if($row['completed_on'] === null) {
              $result[ProjectProgress::OPEN_TASKS] += 1;
            } // if

            $result['object_data'] = array(
              ProjectProgress::TOTAL_TASKS => 0,
              ProjectProgress::OPEN_TASKS => 0,
            );

            // Make type map, so we can use it to query subtasks
            if(!isset($subtask_parent_ids[$row['type']])) {
              $subtask_parent_ids[$row['type']] = array();
            } // if

            $subtask_parent_ids[$row['type']][] = (integer) $row['id'];

            // Make sure that we remember object's milestone
            $milestone_id = (integer) $row['milestone_id'];

            if($milestone_id) {
              $project_object_milestones_map[(integer) $row['id']] = $milestone_id;

              // Refresh milestone counts
              if(isset($result['milestone_data'][$milestone_id])) {
                $result['milestone_data'][$milestone_id][ProjectProgress::TOTAL_TASKS] += 1;
              } else {
                $result['milestone_data'][$milestone_id] = array(
                  ProjectProgress::TOTAL_TASKS => 1,
                  ProjectProgress::OPEN_TASKS => 0,
                );
              } // if

              if($row['completed_on'] === null) {
                $result['milestone_data'][$milestone_id][ProjectProgress::OPEN_TASKS] += 1;
              } // if
            } // if
          } // foreach

          $subtask_parent_conditions = array();
          foreach($subtask_parent_ids as $type => $ids) {
            $subtask_parent_conditions[] = DB::prepare("(parent_type = ? AND parent_id IN (?))", $type, $ids);
          } // foreach
          $subtask_parent_conditions = '(' . implode(' OR ', $subtask_parent_conditions) . ')';

          $subtask_rows = DB::execute("SELECT id, parent_id, completed_on FROM $subtasks_table WHERE $subtask_parent_conditions AND state >= ?", STATE_ARCHIVED);
          if($subtask_rows) {
            foreach($subtask_rows as $subtask_row) {
              $parent_id = (integer) $subtask_row['parent_id'];

              if(!isset($result['object_data'][$parent_id])) {
                $result['object_data'][$parent_id] = array(
                  ProjectProgress::TOTAL_TASKS => 0,
                  ProjectProgress::OPEN_TASKS => 0,
                );
              } // if

              // Total
              $result[ProjectProgress::TOTAL_TASKS] += 1;
              $result['object_data'][$parent_id][ProjectProgress::TOTAL_TASKS] += 1;

              // Open
              if($subtask_row['completed_on'] === null) {
                $result[ProjectProgress::OPEN_TASKS] += 1;
                $result['object_data'][$parent_id][ProjectProgress::OPEN_TASKS] += 1;
              } // if

              if(isset($project_object_milestones_map[$parent_id]) && $project_object_milestones_map[$parent_id]) {
                $milestone_id = $project_object_milestones_map[$parent_id];

                $result['milestone_data'][$milestone_id][ProjectProgress::TOTAL_TASKS] += 1;

                if($subtask_row['completed_on'] === null) {
                  $result['milestone_data'][$milestone_id][ProjectProgress::OPEN_TASKS] += 1;
                } // if
              } // if
            } // foreach
          } // if
        } // if

        return $result;
      });
    } // getProjectProgressCache

    /**
     * @var bool|array
     */
    private static $_completed_progress = false;

    /**
     * Default 'completed' status (for archived or fallback progress)
     *
     * @return array|bool
     */
    static function getCompletedProgress() {
      if (self::$_completed_progress === false) {
        self::$_completed_progress = array(
          self::TOTAL_TASKS => 1,
          self::OPEN_TASKS => 0
        );
      } // if

      return self::$_completed_progress;
    } // getCompletedProgress

    /**
     * @var bool|array
     */
    private static $_quick_progress = false;

    /**
     * @param null|int $project_id
     * @return array
     */
    static function getQuickProgress($project_id = null) {
      if (self::$_quick_progress === false) {
        self::$_quick_progress = AngieApplication::cache()->get('projects_quick_progress', function() {
          $progress = array(); // default

          $project_ids = DB::executeFirstColumn("SELECT id FROM ".TABLE_PREFIX."projects WHERE state >= ?", STATE_ARCHIVED);
          if (is_foreachable($project_ids)) {

            $common_conditions = DB::prepare("type = 'Task' AND state >= ? AND project_id IN (?)", STATE_ARCHIVED, $project_ids);
            // get open & total tasks for projects
            $all_tasks = DB::execute("SELECT project_id, COUNT(id) AS total FROM ".TABLE_PREFIX."project_objects WHERE {$common_conditions} GROUP BY project_id");
            if (is_foreachable($all_tasks)) {
              $all_tasks = $all_tasks->toArrayIndexedBy('project_id');
            } else {
              $all_tasks = array();
            } // if

            $open_tasks = DB::execute("SELECT project_id, COUNT(id) AS total FROM ".TABLE_PREFIX."project_objects WHERE {$common_conditions} AND completed_on IS NULL GROUP BY project_id");
            if (is_foreachable($open_tasks)) {
              $open_tasks = $open_tasks->toArrayIndexedBy('project_id');
            } else {
              $open_tasks = array();
            } // if

            foreach ($project_ids as $project_id) {
              // because db wont return rows where count() = 0 we need force "0" if
              // array doesn't have required $project_id key
              $progress[$project_id] = array(
                ProjectProgress::TOTAL_TASKS => isset($all_tasks[$project_id]) ? $all_tasks[$project_id]['total'] : 0,
                ProjectProgress::OPEN_TASKS => isset($open_tasks[$project_id]) ? $open_tasks[$project_id]['total'] : 0
              );
            } // foreach
          } // if

          return $progress;
        });
      } // if

      if ($project_id) {
        return self::$_quick_progress[$project_id];
      } else {
        return self::$_quick_progress;
      } // if
    } // getQuickProgress

    /**
     * This method renders round project progress. Second parameter defines the type of images used for progress
     *
     * @param Project|int $project
     * @param string $color_class
     *
     * @return string
     */
    static function renderRoundProjectProgress($project, $color_class = 'mono') {
      if ($project instanceof Project) {
        list($total_assignments, $open_assignments) = $project->complete()->isCompleted() ? self::getCompletedProgress() : self::getQuickProgress($project->getId());
      } else {
        list($total_assignments, $open_assignments) = self::getQuickProgress($project);
      } // if

      $completed_assignments  = $total_assignments - $open_assignments;

      if ($total_assignments > 0 && $completed_assignments > 0) {
        if($completed_assignments >= $total_assignments) {
          $return = '<img src="' .  AngieApplication::getImageUrl('progress/progress-' . $color_class . '-100.png', 'complete') . '">';
        } else {
          $percentage = ceil(($completed_assignments / $total_assignments) * 100);

          if($percentage <= 10) {
            $return = '<img src="' .  AngieApplication::getImageUrl('progress/progress-' . $color_class . '-0.png', 'complete') . '">';
          } else if($percentage <= 20) {
            $return = '<img src="' .  AngieApplication::getImageUrl('progress/progress-' . $color_class . '-10.png', 'complete') . '">';
          } else if($percentage <= 30) {
            $return = '<img src="' .  AngieApplication::getImageUrl('progress/progress-' . $color_class . '-20.png', 'complete') . '">';
          } else if($percentage <= 40) {
            $return = '<img src="' .  AngieApplication::getImageUrl('progress/progress-' . $color_class . '-30.png', 'complete') . '">';
          } else if($percentage <= 50) {
            $return = '<img src="' .  AngieApplication::getImageUrl('progress/progress-' . $color_class . '-40.png', 'complete') . '">';
          } else if($percentage <= 60) {
            $return = '<img src="' .  AngieApplication::getImageUrl('progress/progress-' . $color_class . '-50.png', 'complete') . '">';
          } else if($percentage <= 70) {
            $return = '<img src="' .  AngieApplication::getImageUrl('progress/progress-' . $color_class . '-60.png', 'complete') . '">';
          } else if($percentage <= 80) {
            $return = '<img src="' .  AngieApplication::getImageUrl('progress/progress-' . $color_class . '-70.png', 'complete') . '">';
          } else if($percentage <= 90) {
            $return = '<img src="' .  AngieApplication::getImageUrl('progress/progress-' . $color_class . '-80.png', 'complete') . '">';
          } else {
            $return = '<img src="' .  AngieApplication::getImageUrl('progress/progress-' . $color_class . '-90.png', 'complete') . '">';
          } // if
        } // if
      } else {
        $return = '<img src="' .  AngieApplication::getImageUrl('progress/progress-' . $color_class . '-0.png', 'complete') . '">';
      } // if
      return $return;
    } // renderRoundProjectProgress
    
  }