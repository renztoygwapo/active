<?php

  /**
   * Project scheduler implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */

  /**
   * All project schedule related operations
   */
  final class ProjectScheduler {
    
    /**
     * Change project start date
     * 
     * This method changes project start date while making sure that start and 
     * due dates of project items is up to date with the change
     *
     * @param Project $project
     * @param DateValue $reference_first_milestone_date
     * @param DateValue $new_first_milestone_date
     * @throws Exception
     */
    static function rescheduleProject(Project $project, DateValue $reference_first_milestone_date, DateValue $new_first_milestone_date) {
      try {
        DB::beginWork('Rescheduling project @ ' . __CLASS__);
        
        $project_objects_table = TABLE_PREFIX . 'project_objects';
      
        $diff = $new_first_milestone_date->getTimestamp() - $reference_first_milestone_date->getTimestamp();
        
        // Get milestones order by start date, that are not TBD
        $milestones = Milestones::find(array(
          'conditions' => array('project_id = ? AND date_field_1 IS NOT NULL AND due_on IS NOT NULL', $project->getId()),
          'order' => 'date_field_1'
        ));
        
        // Reschedule milestones and cascade rescheduling to related objects
        ProjectScheduler::pushMilestones($milestones, $diff, true);
        
        // Reschedule objects that don't have milestone set
        $reschedule_ids = DB::executeFirstColumn("SELECT id FROM $project_objects_table WHERE type IN (?) AND project_id = ? AND state >= ? AND (milestone_id IS NULL OR milestone_id = ?)", array('Task', 'TodoList'), $project->getId(), STATE_ARCHIVED, 0);
        
        if($reschedule_ids) {
          self::advanceProjectItems($reschedule_ids, $diff);
        } // if
        
        DB::commit('Project rescheduled @ ' . __CLASS__);

        AngieApplication::cache()->removeByModel('project_objects');
      } catch(Exception $e) {
        DB::rollback('Failed to reschedule project @ ' . __CLASS__);
        throw $e;
      } // try
    } // rescheduleProject
    
    /**
     * Push set of milestones by advancing their start date
     *
     * @param Milestone[] $milestones
     * @param integer $advance
     * @param boolean $reschedule_tasks
     * @throws Exception
     */
    static function pushMilestones($milestones, $advance, $reschedule_tasks = false) {
      try {
        DB::beginWork('Rescheduling milestones @ ' . __CLASS__);
        
        $advance_seconds = $advance; // Start value
        
        foreach($milestones as $milestone) {
          $start_on = $milestone->getStartOn();
          $due_on = $milestone->getDueOn();
          
          if($start_on instanceof DateValue && $due_on instanceof DateValue) {
            list($start_on_moved, $due_on_moved) = ProjectScheduler::rescheduleMilestone($milestone, new DateValue($start_on->getTimestamp() + $advance_seconds), new DateValue($due_on->getTimestamp() + $advance_seconds), $reschedule_tasks);

            if($start_on_moved) {
              $advance_seconds += $start_on_moved * 86400;
            } // if
          } // if
        } // foreach
        
        DB::commit('Rescheduled milestones @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to reschedule milestones @ ' . __CLASS__);
        
        throw $e;
      } // try
    } // pushMilestones

    /**
     * Push set of milestones by advancing their start date
     *
     * @param Milestone[] $milestones
     * @param integer $advance
     * @param boolean $reschedule_tasks
     * @throws Exception
     */
    static function pullMilestones($milestones, $advance, $reschedule_tasks = false) {
      try {
        DB::beginWork('Rescheduling milestones @ ' . __CLASS__);

        $advance_seconds = $advance; // Start value

        foreach($milestones as $milestone) {
          $start_on = $milestone->getStartOn();
          $due_on = $milestone->getDueOn();

          if($start_on instanceof DateValue && $due_on instanceof DateValue) {
            list($start_on_moved, $due_on_moved) = ProjectScheduler::rescheduleMilestone($milestone, new DateValue($start_on->getTimestamp() + $advance_seconds), new DateValue($due_on->getTimestamp() + $advance_seconds), $reschedule_tasks);

            if($due_on_moved) {
              $advance_seconds += $due_on_moved * 86400;
            } // if
          } // if
        } // foreach

        DB::commit('Rescheduled milestones @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to reschedule milestones @ ' . __CLASS__);

        throw $e;
      } // try
    } // pullMilestones

    /**
     * Reschedule milestone
     *
     * Change start and / or due date of a milestone while taking care of start
     * and due dates of the other milestones and tasks in the project
     *
     * @param Milestone $milestone
     * @param DateValue $new_start_date
     * @param DateValue $new_due_date
     * @param bool $reschedule_tasks
     * @param Milestone[] $successive_milestones
     * @return array
     * @throws Exception
     */
    static function rescheduleMilestone(Milestone &$milestone, DateValue $new_start_date, DateValue $new_due_date, $reschedule_tasks = false, $successive_milestones = null) {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $start_on_moved = $due_on_moved = $advance_successive_milestones = 0;
      
      $start_on = $milestone->getStartOn();
      $due_on = $milestone->getDueOn();

      // ---------------------------------------------------
      //  Lets get direction for calculating days off, as
      //  well as how we should reschedule successive
      //  milestones
      // ---------------------------------------------------

	    if ($due_on instanceof DateValue && $start_on instanceof DateValue) {
		    if($due_on->getTimestamp() != $new_due_date->getTimestamp()) {
			    $reschedule_based_on_due_date = true;

			    $direction_multiplier = $due_on->getTimestamp() > $new_due_date->getTimestamp() ? -1 : 1;
			    $advance_successive_milestones = ProjectScheduler::calculateNumberOfSecondsToAdvanceSuccessiveMilestones($due_on, $new_due_date, $successive_milestones);
		    } else {
			    $reschedule_based_on_due_date = false;

			    $direction_multiplier = $start_on->getTimestamp() > $new_start_date->getTimestamp() ? -1 : 1;
			    $advance_successive_milestones = ProjectScheduler::calculateNumberOfSecondsToAdvanceSuccessiveMilestones($start_on, $new_start_date, $successive_milestones);
		    } // if
	    } else {
		    $direction_multiplier = -1;
	    } // if

      // ---------------------------------------------------
      //  Skip days off
      // ---------------------------------------------------

      while(!Globalization::isWorkday($new_start_date)) {
        $new_start_date->advance($direction_multiplier * 86400);
        $start_on_moved += $direction_multiplier;
      } // if

      if($start_on_moved) {
        $new_due_date->advance(86400 * $start_on_moved);
      } // if

      while(!Globalization::isWorkday($new_due_date)) {
        $new_due_date->advance($direction_multiplier * 86400);
        $due_on_moved += $direction_multiplier;
      } // if

      // ---------------------------------------------------
      //  Do the rescheduling
      // ---------------------------------------------------
      
      try {
        DB::beginWork('Rescheduling milestone @ ' . __CLASS__);
        
        $milestone->setStartOn($new_start_date);
        $milestone->setDueOn($new_due_date);

        $milestone->save();

	      if ($due_on instanceof DateValue && $start_on instanceof DateValue) {
		      $new_start_date_advance = $new_start_date->getTimestamp() - $start_on->getTimestamp();
		      $new_due_date_advance = $new_due_date->getTimestamp() - $due_on->getTimestamp();
	      } else {
		      $new_start_date_advance = $new_start_date->getTimestamp();
		      $new_due_date_advance = $new_due_date->getTimestamp();
	      } // if

        // Reschedule all related tasks...
        if($reschedule_tasks && $start_on instanceof DateValue && $due_on instanceof DateValue) {
          $milestone_object_ids = DB::executeFirstColumn("SELECT id FROM $project_objects_table WHERE type IN (?) AND milestone_id = ? AND state >= ?", array('TodoList', 'Task'), $milestone->getId(), STATE_ARCHIVED);
          
          if($milestone_object_ids) {
            ProjectScheduler::advanceProjectItems($milestone_object_ids, $new_start_date_advance);
          } // if
        } // if

        // Reschedule successive milestones
        if($advance_successive_milestones && $successive_milestones) {
          if($reschedule_based_on_due_date) {
            ProjectScheduler::pullMilestones($successive_milestones, $advance_successive_milestones, $reschedule_tasks);
          } else {
            ProjectScheduler::pushMilestones($successive_milestones, $advance_successive_milestones, $reschedule_tasks);
          } // if
        } // if
        
        DB::commit('Milestone rescheduled @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to reschedule milestone @ ' . __CLASS__);
        
        throw $e;
      } // try
      
      return array($start_on_moved, $due_on_moved);
    } // rescheduleMilestone

    /**
     * Return number of seconds to advance successive milestones (by ignoring number of days of between the new dates)
     *
     * @param DateValue $old
     * @param DateValue $new
     * @param Milestone[] $successive_milestones
     * @return integer
     */
    static private function calculateNumberOfSecondsToAdvanceSuccessiveMilestones(DateValue $old, DateValue $new, $successive_milestones) {
      if($successive_milestones && is_foreachable($successive_milestones)) {
        $old_date = clone($old);
        $new_date = clone($new);

        $days_between = $old_date->daysBetween($new_date);
        $direction_multiplier = $old_date->getTimestamp() > $new_date->getTimestamp() ? -1 : 1;

        if($days_between < 7) {
          $advance = 0;

          while(!$new_date->isSameDay($old_date)) {
            $old_date->advance($direction_multiplier * 86400);

            if(Globalization::isWorkday($old_date)) {
              $advance += $direction_multiplier * 86400;
            } // if
          } // while

          return $advance;
        } else {
          return $direction_multiplier * $days_between * 86400;
        } // if
      } // if

      return 0;
    } // calculateNumberOfSecondsToAdvanceSuccessiveMilestones

    /**
     * Reschedule a single project object
     *
     * @param ProjectObject $object
     * @param DateValue $due_on
     * @param bool $reschedule_subtasks
     * @throws Exception
     */
    static function rescheduleProjectObject(ProjectObject $object, DateValue $due_on, $reschedule_subtasks = true) {
      if($object instanceof ProjectObject && $object->fieldExists('due_on') && $due_on instanceof DateValue) {
        try {
          while(!Globalization::isWorkday($due_on)) {
            $due_on->advance(86400);
          } // while
          
          DB::beginWork('Rescheduling project object @ ' . __CLASS__);
          
          $old_due_on = $object->getDueOn();
          
          $object->setDueOn($due_on);
          $object->save();
          
          if($reschedule_subtasks && $old_due_on instanceof DateValue && $object instanceof ISubtasks) {
            $object->subtasks()->advanceDueDates($due_on->getTimestamp() - $old_due_on->getTimestamp());
          } // if
          
          DB::commit('Project object rescheduled @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to reschedule project object @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // rescheduleProjectObject

    /**
     * Advance give objects by number of seconds
     *
     * @param array $item_ids
     * @param integer $advance
     * @throws Exception
     */
    private static function advanceProjectItems($item_ids, $advance) {
      if($advance && is_foreachable($item_ids)) {
        try {
          $project_objects_table = TABLE_PREFIX . 'project_objects';
          
          DB::beginWork('Advance project items @ ' . __CLASS__);

          $items = DB::execute("SELECT id, type, due_on FROM $project_objects_table WHERE id IN (?) AND completed_on IS NULL", $item_ids);

          if($items) {
            $direction = $advance < 0 ? -1 : 1;

            foreach($items as $item) {
              if($item['due_on']) {
                $item_due_on = new DateValue($item['due_on']);

                $original_timestamp = $item_due_on->getTimestamp(); // Remember old timestamp, we'll need it later

                if($advance != 0) {
                  $item_due_on->advance($advance);
                } // if

                while(!Globalization::isWorkday($item_due_on)) {
                  $item_due_on->advance($direction * 86400);
                } // while

                $item_due_on_diff = $item_due_on->getTimestamp() - $original_timestamp;

                if($item_due_on_diff != 0) {
                  DB::execute("UPDATE $project_objects_table SET due_on = ? WHERE id = ?", $item_due_on, $item['id']);
                  Subtasks::advanceByParent(array($item['type'], $item['id']), $item_due_on_diff, true);
                } // if
              } else {
                Subtasks::advanceByParent(array($item['type'], $item['id']), $advance, true);
              } // if
            } // foreach
          } // if

          AngieApplication::cache()->removeByModel('project_objects');

          DB::commit('Project items advanced @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to advance project items @ ' . __CLASS__);
          
          throw $e;
        } // try
      } // if
    } // advanceProjectItems

  }