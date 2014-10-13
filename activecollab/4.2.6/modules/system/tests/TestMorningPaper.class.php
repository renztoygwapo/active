<?php

  /**
   * Test morning paper functionality
   *
   * @package activeCollab.module.system
   * @subpackage tests
   */
  class TestMorningPaper extends AngieModelTestCase {

    /**
     * Set up test environment
     */
    function setUp() {
      parent::setUp();

      $json_files = get_files(WORK_PATH, 'json');

      if(is_foreachable($json_files)) {
        foreach($json_files as $json_file) {
          unlink($json_file);
        } // foreach
      } // if

      $thursday_is_off = new DayOff();
      $thursday_is_off->setName('Big Holiday');
      $thursday_is_off->setEventDate(DateValue::makeFromString('2013-12-19'));
      $thursday_is_off->save();
    } // setUp

    /**
     * Tear down test environment
     */
    function tearDown() {
      $json_files = get_files(WORK_PATH, 'json');

      if(is_foreachable($json_files)) {
        foreach($json_files as $json_file) {
          unlink($json_file);
        } // foreach
      } // if

      parent::tearDown();
    } // tearDown

    /**
     * Make sure that test is properly intialized
     */
    function testInitialization() {
      $this->assertTrue(DateValue::makeFromString('2013-12-19')->isDayOff());
      $this->assertTrue(DateValue::makeFromString('2013-12-20')->isWorkday());
      $this->assertFalse(DateValue::makeFromString('2013-12-21')->isWorkday());
    } // testInitialization

    /**
     * Test if snapshots are automatically created
     */
    function testSnapshotCreation() {
      $friday = DateValue::makeFromString('2013-12-20');

      $this->assertTrue($friday->isWorkday());
      $this->assertIsA(MorningPaper::getSnapshot($friday), 'MorningPaperSnapshot');

      $saturday = DateValue::makeFromString('2013-12-21');
      $this->assertFalse($saturday->isWorkday());

      try {
        MorningPaper::getSnapshot($saturday);

        $this->fail('Invalid param expected');
      } catch(InvalidParamError $e) {
        $this->pass('Invalid param exception throw when date is not a workday');
      } // try
    } // testSnapshotCreation

    /**
     * Return snapshot boundaries
     */
    function testSnapshotBoundaries() {
      $snapshot = MorningPaper::getSnapshot(DateValue::makeFromString('2013-12-20'));

      $boundaries = $snapshot->getTodayBoundaries();

      $this->assertEqual($boundaries[0]->toMySQL(), '2013-12-20 00:00:00');
      $this->assertEqual($boundaries[1]->toMySQL(), '2013-12-20 23:59:59');

      $boundaries = $snapshot->getPreviousBusinessDayBoundaries();

      $this->assertEqual($boundaries[0]->toMySQL(), '2013-12-18 00:00:00');
      $this->assertEqual($boundaries[1]->toMySQL(), '2013-12-18 23:59:59');
    } // testSnapshotBoundaries

    /**
     * Test if snapshot is properly created
     */
    function testSnapshot() {
      $logged_user = Users::findById(1);

      $this->assertTrue($logged_user->isLoaded());
      $this->assertIsA($logged_user, 'Administrator');

      $employee = new Member();
      $employee->setAttributes(array(
        'email' => 'second-user@test.com',
        'first_name' => 'Andre',
        'last_name' => 'Agassi',
        'company_id' => 1,
        'password' => 'test',
      ));
      $employee->setState(STATE_VISIBLE);
      $employee->save();

      $this->assertTrue($employee->isLoaded());

      // Prepare a completed project
      $completed_project = new Project();
      $completed_project->setAttributes(array(
        'name' => 'Completed Project',
        'leader_id' => 1,
        'company_id' => 1,
      ));
      $completed_project->setState(STATE_VISIBLE);
      $completed_project->save();

      $this->assertTrue($completed_project->isLoaded());
      $completed_project->users()->add($logged_user);
      $completed_project->users()->add($employee, null, array(
        'task' => ProjectRole::PERMISSION_ACCESS,
        'milestone' => ProjectRole::PERMISSION_NONE,
      ));
      $this->assertTrue($completed_project->users()->isMember($logged_user));
      $this->assertTrue($completed_project->users()->isMember($employee));

      $this->assertTrue(Tasks::canAccess($employee, $completed_project));
      $this->assertFalse(Milestones::canAccess($employee, $completed_project));

      // Completed milestone
      $completed_milestone = new Milestone();
      $completed_milestone->setName('Completed Milestone');
      $completed_milestone->setProject($completed_project);
      $completed_milestone->setCreatedBy($logged_user);
      $completed_milestone->setState(STATE_VISIBLE);
      $completed_milestone->save();

      $this->assertTrue($completed_milestone->isLoaded());

      $completed_milestone->complete()->complete($employee);
      $completed_milestone->setCompletedOn(DateTimeValue::makeFromString('2013-12-18 12:00:00'));
      $completed_milestone->save();

      $this->assertTrue($completed_milestone->complete()->isCompleted());
      $this->assertEqual($completed_milestone->getCompletedOn()->toMySQL(), '2013-12-18 12:00:00');

      // Completed task
      $completed_task = new Task();
      $completed_task->setName('Completed Task');
      $completed_task->setAssigneeId($logged_user->getId());
      $completed_task->setProject($completed_project);
      $completed_task->setCreatedBy($logged_user);
      $completed_task->setState(STATE_VISIBLE);
      $completed_task->save();

      $this->assertTrue($completed_task->isLoaded());

      $completed_subtask = $completed_task->subtasks()->newSubtask();
      $completed_subtask->setBody('Completed subtask');
      $completed_subtask->setCreatedBy($logged_user);
      $completed_subtask->setState(STATE_VISIBLE);
      $completed_subtask->setAssigneeId($logged_user->getId());
      $completed_subtask->save();

      $this->assertTrue($completed_subtask->isLoaded());

      $completed_task->complete()->complete($employee);
      $completed_task->setCompletedOn(DateTimeValue::makeFromString('2013-12-18 12:00:00'));
      $completed_task->save();

      $this->assertTrue($completed_task->complete()->isCompleted());
      $this->assertEqual($completed_task->getCompletedOn()->toMySQL(), '2013-12-18 12:00:00');

      $completed_subtask->setCompletedOn(DateTimeValue::makeFromString('2013-12-18 12:00:00'));
      $completed_subtask->save();

      $this->assertTrue($completed_subtask->complete()->isCompleted());
      $this->assertEqual($completed_subtask->getCompletedOn()->toMySQL(), '2013-12-18 12:00:00');

      // Complete the completed project
      $completed_project->complete()->complete($employee);
      $completed_project->setCompletedOn(DateTimeValue::makeFromString('2013-12-18 12:00:00'));
      $completed_project->save();

      $this->assertTrue($completed_project->complete()->isCompleted());
      $this->assertEqual($completed_project->getCompletedOn()->toMySQL(), '2013-12-18 12:00:00');

      // Prepare a project that logged user does not have access to
      $other_project = new Project();
      $other_project->setAttributes(array(
        'name' => 'Other Project',
        'leader_id' => 1,
        'company_id' => 1,
      ));
      $other_project->setState(STATE_VISIBLE);
      $other_project->save();

      $other_project->complete()->complete($employee);
      $other_project->setCompletedOn(DateTimeValue::makeFromString('2013-12-18 12:00:00'));
      $other_project->save();

      $this->assertFalse($other_project->users()->isMember($logged_user));
      $this->assertTrue($other_project->complete()->isCompleted());
      $this->assertEqual($other_project->getCompletedOn()->toMySQL(), '2013-12-18 12:00:00');

      // Old project with an old task
      $old_project = new Project();
      $old_project->setAttributes(array(
        'name' => 'Other Project',
        'leader_id' => 1,
        'company_id' => 1,
      ));
      $old_project->setState(STATE_VISIBLE);
      $old_project->save();

      $old_project->users()->add($logged_user);

      $old_project_task = new Task();
      $old_project_task->setName('Old Completed Task');
      $old_project_task->setProject($old_project);
      $old_project_task->setCreatedBy($logged_user);
      $old_project_task->setState(STATE_VISIBLE);
      $old_project_task->save();

      $this->assertTrue($old_project_task->isLoaded());

      $old_project_task->complete()->complete($employee);
      $old_project_task->setCompletedOn(DateTimeValue::makeFromString('2013-12-18 12:00:00'));
      $old_project_task->save();

      $this->assertTrue($old_project_task->complete()->isCompleted());
      $this->assertEqual($old_project_task->getCompletedOn()->toMySQL(), '2013-12-18 12:00:00');

      $old_project->complete()->complete($employee);
      $old_project->setCompletedOn(DateTimeValue::makeFromString('2013-12-17 12:00:00'));
      $old_project->save();

      $this->assertTrue($old_project->users()->isMember($logged_user));
      $this->assertTrue($old_project->complete()->isCompleted());
      $this->assertEqual($old_project->getCompletedOn()->toMySQL(), '2013-12-17 12:00:00');

      // ---------------------------------------------------
      //  Open Project
      // ---------------------------------------------------

      $open_project = new Project();
      $open_project->setAttributes(array(
        'name' => 'Open Project',
        'leader_id' => 1,
        'company_id' => 1,
      ));
      $open_project->setState(STATE_VISIBLE);
      $open_project->save();

      $open_project->users()->add($logged_user);
      $open_project->users()->add($employee, null, array(
        'task' => ProjectRole::PERMISSION_ACCESS,
        'milestone' => ProjectRole::PERMISSION_NONE,
      ));

      $this->assertTrue($open_project->isLoaded());
      $this->assertTrue($open_project->users()->isMember($logged_user));
      $this->assertTrue($open_project->users()->isMember($employee));
      $this->assertTrue(Tasks::canAccess($employee, $open_project));
      $this->assertFalse(Milestones::canAccess($employee, $open_project));

      $late_milestone = new Milestone();
      $late_milestone->setName('Late Milestone');
      $late_milestone->setProject($open_project);
      $late_milestone->setDueOn(DateValue::makeFromString('2013-11-11'));
      $late_milestone->setCreatedBy($logged_user);
      $late_milestone->setState(STATE_VISIBLE);
      $late_milestone->setAssigneeId($logged_user->getId());
      $late_milestone->save();

      $this->assertTrue($late_milestone->isLoaded());

      $today_task = new Task();
      $today_task->setName('Today Task');
      $today_task->setDueOn(DateValue::makeFromString('2013-12-20'));
      $today_task->setProject($open_project);
      $today_task->setCreatedBy($logged_user);
      $today_task->setState(STATE_VISIBLE);
      $today_task->setAssigneeId($logged_user->getId());
      $today_task->save();

      $this->assertTrue($today_task->isLoaded());

      $yesterday_subtask = $today_task->subtasks()->newSubtask();
      $yesterday_subtask->setDueOn(DateValue::makeFromString('2013-12-19'));
      $yesterday_subtask->setBody('Yesterday subtask');
      $yesterday_subtask->setCreatedBy($logged_user);
      $yesterday_subtask->setState(STATE_VISIBLE);
      $yesterday_subtask->setAssigneeId($logged_user->getId());
      $yesterday_subtask->save();

      $this->assertTrue($yesterday_subtask->isLoaded());

      $today_unassigned_task = new Task();
      $today_unassigned_task->setName('Today task, but not assigned to current user');
      $today_unassigned_task->setDueOn(DateValue::makeFromString('2013-12-20'));
      $today_unassigned_task->setProject($open_project);
      $today_unassigned_task->setCreatedBy($logged_user);
      $today_unassigned_task->setState(STATE_VISIBLE);
      $today_unassigned_task->save();

      $late_milestone_for_employee = new Milestone();
      $late_milestone_for_employee->setName('Late Milestone');
      $late_milestone_for_employee->setProject($open_project);
      $late_milestone_for_employee->setDueOn(DateValue::makeFromString('2013-11-11'));
      $late_milestone_for_employee->setCreatedBy($logged_user);
      $late_milestone_for_employee->setState(STATE_VISIBLE);
      $late_milestone_for_employee->setAssigneeId($employee->getId());
      $late_milestone_for_employee->save();

      $this->assertTrue($late_milestone_for_employee->isLoaded());

      $today_task_for_employee = new Task();
      $today_task_for_employee->setName('Today task, assigned to employee');
      $today_task_for_employee->setDueOn(DateValue::makeFromString('2013-12-20'));
      $today_task_for_employee->setProject($open_project);
      $today_task_for_employee->setCreatedBy($logged_user);
      $today_task_for_employee->setAssigneeId($employee->getId());
      $today_task_for_employee->setState(STATE_VISIBLE);
      $today_task_for_employee->save();

      $this->assertTrue($today_task_for_employee->isLoaded());

      // ---------------------------------------------------
      //  Test project manager
      // ---------------------------------------------------

      $snapshot = MorningPaper::getSnapshot(DateValue::makeFromString('2013-12-20'));

      $this->assertIsA($snapshot, 'MorningPaperSnapshot');

      /**
       * Previous event exists
       *
       * @param array $data
       * @param string $event
       * @param integer $object_id
       * @param integer $project_id
       * @param Closure $on_found
       * @return bool
       */
      $previous_day_event_exists = function($data, $event, $object_id, $project_id, $on_found = null) {
        if($data && $data[0] && isset($data[0][$project_id])) {
          foreach($data[0][$project_id]['events'] as $v) {
            if($v['event'] == $event && $v['id'] == $object_id) {
              if($on_found instanceof Closure) {
                $on_found->__invoke($v);
              } // if

              return true;
            } // if
          } // foreach
        } // if

        return false;
      };

      /**
       * Loop through events and return true if a particular event is found
       *
       * @param array $data
       * @param string $event
       * @param integer $object_id
       * @param integer $project_id
       * @return bool
       */
      $today_event_exists = function($data, $event, $object_id, $project_id) {
        if($data && $data[1]) {
          foreach($data[1] as $v) {
            if($v['event'] == $event && $v['project_id'] == $project_id && $v['id'] == $object_id) {
              return true;
            } // if
          } // foreach
        } // if

        return false;
      };

      $data_for_logged_user = $snapshot->getDataFor($logged_user);
      $all_data_for_logged_user = $snapshot->getDataFor($logged_user, true);

      $this->assertTrue($previous_day_event_exists($data_for_logged_user, MorningPaper::PROJECT_COMPLETED, $completed_project->getId(), $completed_project->getId(), function($event) {
        $this->assertEqual($event['action_by'], 'Andre A.');
      }));
      $this->assertTrue($previous_day_event_exists($data_for_logged_user, MorningPaper::MILESTONE_COMPLETED, $completed_milestone->getId(), $completed_project->getId(), function($event) {
        $this->assertEqual($event['action_by'], 'Andre A.');
      }));
      $this->assertTrue($previous_day_event_exists($data_for_logged_user, MorningPaper::TASK_COMPLETED, $completed_task->getId(), $completed_project->getId(), function($event) {
        $this->assertEqual($event['action_by'], 'Andre A.');
      }));
      $this->assertTrue($previous_day_event_exists($data_for_logged_user, MorningPaper::SUBTASK_COMPLETED, $completed_subtask->getId(), $completed_project->getId(), function($event) {
        $this->assertEqual($event['action_by'], 'Andre A.');
      }));

      $this->assertFalse($previous_day_event_exists($data_for_logged_user, MorningPaper::PROJECT_COMPLETED, $other_project->getId(), $other_project->getId(), function($event) {
        $this->assertEqual($event['action_by'], 'Andre A.');
      }));
      $this->assertTrue($previous_day_event_exists($all_data_for_logged_user, MorningPaper::PROJECT_COMPLETED, $other_project->getId(), $other_project->getId(), function($event) {
        $this->assertEqual($event['action_by'], 'Andre A.');
      }));

      // Old project that should not be included in the snapshot
      $this->assertFalse($previous_day_event_exists($data_for_logged_user, MorningPaper::PROJECT_COMPLETED, $old_project->getId(), $old_project->getId()));
      $this->assertFalse($previous_day_event_exists($all_data_for_logged_user, MorningPaper::PROJECT_COMPLETED, $old_project->getId(), $old_project->getId()));
      $this->assertFalse($previous_day_event_exists($data_for_logged_user, MorningPaper::TASK_COMPLETED, $old_project_task->getId(), $old_project->getId()));
      $this->assertFalse($previous_day_event_exists($all_data_for_logged_user, MorningPaper::TASK_COMPLETED, $old_project_task->getId(), $old_project->getId()));

      $this->assertTrue($today_event_exists($data_for_logged_user, MorningPaper::MILESTONE_DUE, $late_milestone->getId(), $open_project->getId()));
      $this->assertTrue($today_event_exists($data_for_logged_user, MorningPaper::TASK_DUE, $today_task->getId(), $open_project->getId()));
      $this->assertFalse($today_event_exists($data_for_logged_user, MorningPaper::TASK_DUE, $today_unassigned_task->getId(), $open_project->getId()));
      $this->assertTrue($today_event_exists($data_for_logged_user, MorningPaper::SUBTASK_DUE, $yesterday_subtask->getId(), $open_project->getId()));

      // ---------------------------------------------------
      //  Employee (can access tasks, can't access milestones)
      // ---------------------------------------------------

      $data_for_emploee = $snapshot->getDataFor($employee, true);

      $this->assertFalse($previous_day_event_exists($data_for_emploee, MorningPaper::PROJECT_COMPLETED, $completed_project->getId(), $completed_project->getId()));
      $this->assertFalse($previous_day_event_exists($data_for_emploee, MorningPaper::MILESTONE_COMPLETED, $completed_milestone->getId(), $completed_project->getId()));
      $this->assertFalse($previous_day_event_exists($data_for_emploee, MorningPaper::TASK_COMPLETED, $completed_task->getId(), $completed_project->getId()));
      $this->assertFalse($previous_day_event_exists($data_for_emploee, MorningPaper::SUBTASK_COMPLETED, $completed_subtask->getId(), $completed_project->getId()));

      $this->assertFalse($today_event_exists($data_for_emploee, MorningPaper::MILESTONE_DUE, $late_milestone_for_employee->getId(), $open_project->getId()));
      $this->assertTrue($today_event_exists($data_for_emploee, MorningPaper::TASK_DUE, $today_task_for_employee->getId(), $open_project->getId()));
    } // testSnapshot

  }