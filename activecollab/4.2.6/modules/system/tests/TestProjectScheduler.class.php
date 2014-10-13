<?php

  /**
   * Test scheduler
   */
  class TestProjectScheduler extends AngieModelTestCase {

    /**
     * Monday
     *
     * @var DateValue
     */
    private $monday;

    /**
     * Next monday
     *
     * @var DateValue
     */
    private $next_monday;

    /**
     * Tuesday
     *
     * @var DateValue
     */
    private $tuesday;

    /**
     * Next Tuesday
     *
     * @var DateValue
     */
    private $next_tuesday;

    /**
     * Wednesday
     *
     * @var DateValue
     */
    private $wednesday;

    /**
     * Next Wednesday
     *
     * @var DateValue
     */
    private $next_wednesday;

    /**
     * Thursday
     *
     * @var DateValue
     */
    private $thursday;

    /**
     * Next Thursday
     *
     * @var DateValue
     */
    private $next_thursday;

    /**
     * Friday
     *
     * @var DateValue
     */
    private $friday;

    /**
     * Next Friday
     *
     * @var DateValue
     */
    private $next_friday;

    /**
     * Logged user
     *
     * @var User
     */
    private $logged_user;

    /**
     * Active project
     *
     * @var Project
     */
    private $active_project;

    /**
     * Active milestone
     *
     * @var Milestone
     */
    private $active_milestone;

    /**
     * Active task
     *
     * @var Task
     */
    private $active_task;

    /**
     * Second user
     *
     * @var User
     */
    private $second_user;

    /**
     * Set up test environment
     */
    function setUp() {
      parent::setUp();
      
      $this->monday = new DateValue('2012-03-19');
      $this->next_monday = new DateValue('2012-03-26');
      $this->tuesday = new DateValue('2012-03-20');
      $this->next_tuesday = new DateValue('2012-03-27');
      $this->wednesday = new DateValue('2012-03-21');
      $this->next_wednesday = new DateValue('2012-03-28');
      $this->thursday = new DateValue('2012-03-22');
      $this->next_thursday = new DateValue('2012-03-29');
      $this->friday = new DateValue('2012-03-23');
      $this->next_friday = new DateValue('2012-03-30');

      $this->logged_user = Users::findById(1);
      
      $this->second_user = new Administrator();
      $this->second_user->setAttributes(array(
        'email' => 'second-user@test.com', 
        'company_id' => 1, 
        'password' => 'test', 
        'role_id' => 1, 
      ));
      $this->second_user->setState(STATE_VISIBLE);
      $this->second_user->save();
      
      $this->active_project = new Project();
      $this->active_project->setAttributes(array(
        'name' => 'Test Project', 
        'leader_id' => 1, 
        'company_id' => 1, 
      ));
      $this->active_project->save();
      
      $this->active_milestone = new Milestone();
      $this->active_milestone->setAttributes(array(
        'name' => 'Test Subject', 
      ));
      $this->active_milestone->setProject($this->active_project);
      $this->active_milestone->setCreatedBy($this->logged_user);
      $this->active_milestone->setStartOn($this->monday);
      $this->active_milestone->setDueOn($this->monday);
      $this->active_milestone->setState(STATE_VISIBLE);
      $this->active_milestone->save();
      
      $this->active_task = new Task();
      $this->active_task->setName('Test task');
      $this->active_task->setProject($this->active_project);
      $this->active_task->setMilestone($this->active_milestone);
      $this->active_task->setCreatedBy($this->logged_user);
      $this->active_task->setDueOn($this->tuesday);
      $this->active_task->setState(STATE_VISIBLE);
      $this->active_task->setVisibility(VISIBILITY_NORMAL);
      $this->active_task->save();
      
      $subtask = $this->active_task->subtasks()->newSubtask();
      
      $subtask->setAttributes(array(
        'body' => 'Subtask text', 
      ));
      $subtask->setCreatedBy($this->logged_user);
      $subtask->setDueOn($this->wednesday);
      $subtask->setState(STATE_VISIBLE);
      $subtask->save();
    } // setUp

    /**
     * Test if everything is properly initalised
     */
    function testInitialization() {
      $this->assertEqual($this->monday->toMySQL(), '2012-03-19', 'Monday is properly set');
      $this->assertEqual($this->next_monday->toMySQL(), '2012-03-26', 'Monday is properly set');
      $this->assertEqual($this->tuesday->toMySQL(), '2012-03-20', 'Tuesday is properly set');
      $this->assertEqual($this->next_tuesday->toMySQL(), '2012-03-27', 'Tuesday is properly set');
      $this->assertEqual($this->wednesday->toMySQL(), '2012-03-21', 'Wednesday is properly set');
      $this->assertEqual($this->next_wednesday->toMySQL(), '2012-03-28', 'Wednesday is properly set');
      $this->assertEqual($this->thursday->toMySQL(), '2012-03-22', 'Thursday is properly set');
      $this->assertEqual($this->next_thursday->toMySQL(), '2012-03-29', 'Thursday is properly set');
      $this->assertEqual($this->friday->toMySQL(), '2012-03-23', 'Friday is properly set');
      $this->assertEqual($this->next_friday->toMySQL(), '2012-03-30', 'Friday is properly set');

      $this->assertTrue($this->logged_user->isLoaded(), 'Logged user loaded');
      $this->assertTrue($this->active_project->isLoaded(), 'Active project is created');

      $this->assertTrue($this->active_milestone->isLoaded(), 'Test milestone is created');
      $this->assertEqual(ApplicationObjects::getRememberedContext($this->active_milestone), ApplicationObjects::getContext($this->active_project) . '/milestones/' . $this->active_milestone->getId(), 'Test milestone context is OK');
      $this->assertEqual($this->active_milestone->getStartOn()->toMySQL(), $this->monday->toMySQL(), 'Milestone start date is set');
      $this->assertEqual($this->active_milestone->getDueOn()->toMySQL(), $this->monday->toMySQL(), 'Milestone due date is set');

      $this->assertTrue($this->active_task->isLoaded(), 'Test task is created');
      $this->assertEqual(ApplicationObjects::getRememberedContext($this->active_task), ApplicationObjects::getContext($this->active_project) . '/tasks/normal/' . $this->active_task->getId(), 'Test task context is OK');
      $this->assertEqual($this->active_task->getMilestoneId(), $this->active_milestone->getId(), 'Task milestone is set');
      $this->assertEqual($this->active_task->getDueOn()->toMySQL(), $this->tuesday->toMySQL(), 'Task due date is set');

      $this->assertEqual($this->active_task->subtasks()->count($this->logged_user, false), 1, 'Subtask is created');
      $this->assertIsA($this->active_task->subtasks()->get($this->logged_user)->getRowAt(0), 'Subtask', 'First row is subtak');
      $this->assertEqual($this->active_task->subtasks()->get($this->logged_user)->getRowAt(0)->getDueOn()->toMySQL(), $this->wednesday->toMySQL(), 'Subtask due date is set');
    } // testInitialization

    /**
     * Test reschedule task and subtasks
     */
    function testRescheduleTaskWithSubtasks() {
      ProjectScheduler::rescheduleProjectObject($this->active_task, $this->wednesday, true);

      $this->assertEqual($this->active_task->getDueOn()->toMySQL(), $this->wednesday->toMySQL(), 'Task has been rescheduled');
      $this->assertEqual($this->active_task->subtasks()->get($this->logged_user)->getRowAt(0)->getDueOn()->toMySQL(), $this->thursday->toMySQL(), 'Subtask has been rescheduled');
    } // testRescheduleTaskWithSubtasks

    /**
     * Test reschedule task, but not subtasks
     */
    function testRescheduleOnlyTask() {
      ProjectScheduler::rescheduleProjectObject($this->active_task, $this->wednesday, false);

      $this->assertEqual($this->active_task->getDueOn()->toMySQL(), $this->wednesday->toMySQL(), 'Task has been rescheduled');
      $this->assertEqual($this->active_task->subtasks()->get($this->logged_user)->getRowAt(0)->getDueOn()->toMySQL(), $this->wednesday->toMySQL(), 'Subtask has been rescheduled');
    } // testRescheduleOnlyTask

    /**
     * Test reschedule milestone and cascading to tasks
     */
    function testRescheduleMilestoneAndTasks() {
      ProjectScheduler::rescheduleMilestone($this->active_milestone, $this->tuesday, $this->tuesday, true);

      $this->assertEqual($this->active_milestone->getStartOn()->toMySQL(), $this->tuesday->toMySQL(), 'Milestone start date has been moved');
      $this->assertEqual($this->active_milestone->getDueOn()->toMySQL(), $this->tuesday->toMySQL(), 'Milestone due date has been moved');

      $this->active_task = Tasks::findById($this->active_task->getId()); // Reload

      $this->assertEqual($this->active_task->getDueOn()->toMySQL(), $this->wednesday->toMySQL(), 'Task has been rescheduled');
      $this->assertEqual($this->active_task->subtasks()->get($this->logged_user)->getRowAt(0)->getDueOn()->toMySQL(), $this->thursday->toMySQL(), 'Subtask has been rescheduled');
    } // testRescheduleMilestoneAndTasks

    // ---------------------------------------------------
    //  Test milestone reschedling
    // ---------------------------------------------------

    /**
     * Test pull when both start and due date are changed for the same number of days
     */
    function testRescheduleWithoutSpanChange() {
      $first_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'First milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $this->monday,
        'due_on' => $this->tuesday,
        'state' => STATE_VISIBLE,
      ), true);

      $this->assertTrue($first_milestone->isLoaded());

      $second_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'Second milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $this->wednesday,
        'due_on' => $this->thursday,
        'state' => STATE_VISIBLE,
      ), true);

      $this->assertTrue($second_milestone->isLoaded());

      $third_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'Third milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $this->friday,
        'due_on' => $this->friday,
        'state' => STATE_VISIBLE,
      ), true);

      $this->assertTrue($third_milestone->isLoaded());

      if($first_milestone instanceof Milestone && $second_milestone instanceof Milestone && $third_milestone instanceof Milestone) {

        // Move forward
        ProjectScheduler::rescheduleMilestone($first_milestone, $this->next_monday, $this->next_tuesday, true, array(&$second_milestone, &$third_milestone));

        $this->assertEqual($first_milestone->getStartOn()->toMySQL(), $this->next_monday->toMySQL());
        $this->assertEqual($first_milestone->getDueOn()->toMySQL(), $this->next_tuesday->toMySQL());

        $this->assertEqual($second_milestone->getStartOn()->toMySQL(), $this->next_wednesday->toMySQL());
        $this->assertEqual($second_milestone->getDueOn()->toMySQL(), $this->next_thursday->toMySQL());

        $this->assertEqual($third_milestone->getStartOn()->toMySQL(), $this->next_friday->toMySQL());
        $this->assertEqual($third_milestone->getDueOn()->toMySQL(), $this->next_friday->toMySQL());

        // Move back
        ProjectScheduler::rescheduleMilestone($first_milestone, $this->monday, $this->tuesday, true, array(&$second_milestone, &$third_milestone));

        $this->assertEqual($first_milestone->getStartOn()->toMySQL(), $this->monday->toMySQL());
        $this->assertEqual($first_milestone->getDueOn()->toMySQL(), $this->tuesday->toMySQL());

        $this->assertEqual($second_milestone->getStartOn()->toMySQL(), $this->wednesday->toMySQL());
        $this->assertEqual($second_milestone->getDueOn()->toMySQL(), $this->thursday->toMySQL());

        $this->assertEqual($third_milestone->getStartOn()->toMySQL(), $this->friday->toMySQL());
        $this->assertEqual($third_milestone->getDueOn()->toMySQL(), $this->friday->toMySQL());
      } else {
        $this->fail('Invalid milestone instances returned by ProjectObject::create("Milestone")');
      } // if
    } // testRescheduleWithoutSpanChange

    /**
     * Test pull when only due date changed, but start date remains the same
     */
    function testRescheduleWithDueDateChange() {
      $first_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'First milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $this->monday,
        'due_on' => $this->tuesday,
      ));
      $first_milestone->setState(STATE_VISIBLE);
      $first_milestone->save();

      $this->assertTrue($first_milestone->isLoaded());

      $second_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'Second milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $this->wednesday,
        'due_on' => $this->thursday,
      ));
      $second_milestone->setState(STATE_VISIBLE);
      $second_milestone->save();

      $this->assertTrue($second_milestone->isLoaded());

      $third_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'Third milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $this->friday,
        'due_on' => $this->friday,
      ));
      $third_milestone->setState(STATE_VISIBLE);
      $third_milestone->save();

      $this->assertTrue($third_milestone->isLoaded());

      if($first_milestone instanceof Milestone && $second_milestone instanceof Milestone && $third_milestone instanceof Milestone) {
        ProjectScheduler::rescheduleMilestone($first_milestone, $this->monday, $this->thursday, true, array(&$second_milestone, &$third_milestone));

        $first_milestone = DataObjectPool::get('Milestone', $first_milestone->getId(), null, true);
        $second_milestone = DataObjectPool::get('Milestone', $second_milestone->getId(), null, true);
        $third_milestone = DataObjectPool::get('Milestone', $third_milestone->getId(), null, true);

        $this->assertEqual($first_milestone->getStartOn()->toMySQL(), $this->monday->toMySQL());
        $this->assertEqual($first_milestone->getDueOn()->toMySQL(), $this->thursday->toMySQL());

        $this->assertEqual($second_milestone->getStartOn()->toMySQL(), $this->friday->toMySQL());
        $this->assertEqual($second_milestone->getDueOn()->toMySQL(), $this->monday->advance(7 * 24 * 60 * 60, false)->toMySQL());

        $this->assertEqual($third_milestone->getStartOn()->toMySQL(), $this->tuesday->advance(7 * 24 * 60 * 60, false)->toMySQL());
        $this->assertEqual($third_milestone->getDueOn()->toMySQL(), $this->tuesday->advance(7 * 24 * 60 * 60, false)->toMySQL());
      } else {
        $this->fail('Invalid milestone instances returned by ProjectObject::create("Milestone")');
      } // if
    } // testRescheduleWithDueDateChange

    /**
     * Test pull when both start and due date are changed
     */
    function testRescheduleWithStartAndDueDateChange() {
      $first_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'First milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $this->monday,
        'due_on' => $this->tuesday,
        'state' => STATE_VISIBLE,
      ), true);

      $this->assertTrue($first_milestone->isLoaded());

      $second_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'Second milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $this->wednesday,
        'due_on' => $this->thursday,
        'state' => STATE_VISIBLE,
      ), true);

      $this->assertTrue($second_milestone->isLoaded());

      $third_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'Third milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $this->friday,
        'due_on' => $this->friday,
        'state' => STATE_VISIBLE,
      ), true);

      $this->assertTrue($third_milestone->isLoaded());

      if($first_milestone instanceof Milestone && $second_milestone instanceof Milestone && $third_milestone instanceof Milestone) {
        ProjectScheduler::rescheduleMilestone($first_milestone, $this->tuesday, $this->thursday, true, array(&$second_milestone, &$third_milestone));

        $this->assertEqual($first_milestone->getStartOn()->toMySQL(), $this->tuesday->toMySQL(), 'Start is still Monday');
        $this->assertEqual($first_milestone->getDueOn()->toMySQL(), $this->thursday->toMySQL(), 'Due is moved to Thursday (+2 days)');

        $this->assertEqual($second_milestone->getStartOn()->toMySQL(), $this->friday->toMySQL(), 'Start is moved +2 days');
        $this->assertEqual($second_milestone->getDueOn()->toMySQL(), $this->monday->advance(7 * 24 * 60 * 60, false)->toMySQL(), 'Due is moved +2 days and system skipped weekend');
        $this->assertEqual($third_milestone->getStartOn()->toMySQL(), $this->tuesday->advance(7 * 24 * 60 * 60, false)->toMySQL()/*, 'Due is moved +2 days and system skipped weekend'*/);
        $this->assertEqual($third_milestone->getDueOn()->toMySQL(), $this->tuesday->advance(7 * 24 * 60 * 60, false)->toMySQL()/*, 'Due is moved +2 days and system skipped weekend'*/);
      } else {
        $this->fail('Invalid milestone instances returned by ProjectObject::create("Milestone")');
      } // if
    } // testRescheduleWithStartAndDueDateChange

    /**
     * Test cascading when only start date is changed
     */
    function testPushMilestonesWithStartDateChage() {
      $first_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'First milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $this->monday,
        'due_on' => $this->friday,
        'state' => STATE_VISIBLE,
      ), true);

      $this->assertTrue($first_milestone->isLoaded());

      $second_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'Second milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $this->tuesday,
        'due_on' => $this->wednesday,
        'state' => STATE_VISIBLE,
      ), true);

      $this->assertTrue($second_milestone->isLoaded());

      $third_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'Third milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $this->friday,
        'due_on' => $this->friday,
        'state' => STATE_VISIBLE,
      ), true);

      $this->assertTrue($third_milestone->isLoaded());

      if($first_milestone instanceof Milestone && $second_milestone instanceof Milestone && $third_milestone instanceof Milestone) {

        // Just check start date, but keep due date the same
        ProjectScheduler::rescheduleMilestone($first_milestone, $this->tuesday, $this->friday, true, array(&$second_milestone, &$third_milestone));

        $this->assertEqual($first_milestone->getStartOn()->toMySQL(), $this->tuesday->toMySQL());
        $this->assertEqual($first_milestone->getDueOn()->toMySQL(), $this->friday->toMySQL());

        // Moved by one day
        $this->assertEqual($second_milestone->getStartOn()->toMySQL(), $this->wednesday->toMySQL());
        $this->assertEqual($second_milestone->getDueOn()->toMySQL(), $this->thursday->toMySQL());

        // Moved by one day, but hit weekend, so it needs to be moved to next Monday
        $this->assertEqual($third_milestone->getStartOn()->toMySQL(), $this->monday->advance(7 * 24 * 60 * 60, false)->toMySQL());
        $this->assertEqual($third_milestone->getDueOn()->toMySQL(), $this->monday->advance(7 * 24 * 60 * 60, false)->toMySQL());
      } else {
        $this->fail('Invalid milestone instances returned by ProjectObject::create("Milestone")');
      } // if
    } // testRescheduleWithStartAndDueDateChange

    /**
     * Test cascading when only start date is changed
     */
    function testMilestoneReschedulingInOpositeDirection() {
      $first_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'First milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $this->monday,
        'due_on' => $this->wednesday,
        'state' => STATE_VISIBLE,
      ), true);

      $this->assertTrue($first_milestone->isLoaded());

      $second_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'Second milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $this->thursday,
        'due_on' => $this->friday,
        'state' => STATE_VISIBLE,
      ), true);

      $this->assertTrue($second_milestone->isLoaded());

      $third_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'Third milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $this->monday->advance(7 * 24 * 60 * 60, false),
        'due_on' => $this->monday->advance(7 * 24 * 60 * 60, false),
        'state' => STATE_VISIBLE,
      ), true);

      $this->assertTrue($third_milestone->isLoaded());

      $first_task = ProjectObjects::create('Task', array(
        'name' => 'First task',
        'project_id' => $this->active_project->getId(),
        'milestone_id' => $third_milestone->getId(),
        'due_on' => $third_milestone->getDueOn(),
        'visibility' => VISIBILITY_NORMAL,
      ));
      $first_task->setState(STATE_VISIBLE);
      $first_task->save();

      $this->assertIsA($first_task, 'Task');
      $this->assertEqual($first_task->getState(), STATE_VISIBLE);
      $this->assertEqual($first_task->getProjectId(), $this->active_project->getId());
      $this->assertEqual($first_task->getMilestoneId(), $third_milestone->getId());

      $subtask = $first_task->subtasks()->newSubtask();
      $subtask->setBody('Test subtask');
      $subtask->setState(STATE_VISIBLE);
      $subtask->setDueOn($first_task->getDueOn());
      $subtask->save();

      $this->assertIsA($subtask, 'Subtask');
      $this->assertEqual($subtask->getState(), STATE_VISIBLE);
      $this->assertTrue($subtask->getParent()->is($first_task));

      if($first_milestone instanceof Milestone && $second_milestone instanceof Milestone && $third_milestone instanceof Milestone) {

        // Move due date just one day back and see if successive milestones are properly rescheduled
        ProjectScheduler::rescheduleMilestone($first_milestone, $this->monday, $this->tuesday, true, array(&$second_milestone, &$third_milestone));

        $this->assertEqual($first_milestone->getStartOn()->toMySQL(), $this->monday->toMySQL());
        $this->assertEqual($first_milestone->getDueOn()->toMySQL(), $this->tuesday->toMySQL());

        // Moved by one day
        $this->assertEqual($second_milestone->getStartOn()->toMySQL(), $this->wednesday->toMySQL());
        $this->assertEqual($second_milestone->getDueOn()->toMySQL(), $this->thursday->toMySQL());

        // Moved by one day, but hit weekend, so it needs to be moved to next Monday
        $this->assertEqual($third_milestone->getStartOn()->toMySQL(), $this->friday->toMySQL());
        $this->assertEqual($third_milestone->getDueOn()->toMySQL(), $this->friday->toMySQL());

        $reloaded_task = DataObjectPool::get('Task', $first_task->getId(), null, true);

        $this->assertIsA($reloaded_task, 'Task');
        $this->assertEqual($reloaded_task->getDueOn()->toMySQL(), $this->friday->toMySQL());

        $reloaded_task = DataObjectPool::get('Subtask', $subtask->getId(), null, true);

        $this->assertIsA($reloaded_task, 'Subtask');
        $this->assertEqual($reloaded_task->getDueOn()->toMySQL(), $this->friday->toMySQL());
      } else {
        $this->fail('Invalid milestone instances returned by ProjectObject::create("Milestone")');
      } // if
    } // testMilestoneReschedulingInOpositeDirection

    /**
     * Make sure that only work days are taken into account when rescheduling
     */
    function testWeekendSkipping() {
      $next_monday = $this->monday->advance(7 * 24 * 60 * 60, false);
      $next_tuesday = $this->tuesday->advance(7 * 24 * 60 * 60, false);
      $the_other_monday = $this->monday->advance(14 * 24 * 60 * 60, false);
      $the_other_tuesday = $this->tuesday->advance(14 * 24 * 60 * 60, false);

      $first_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'First milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $this->friday,
        'due_on' => $this->friday,
        'state' => STATE_VISIBLE,
      ), true);

      $this->assertTrue($first_milestone->isLoaded());
      $this->assertEqual($first_milestone->getStartOn()->toMySQL(), $this->friday->toMySQL());
      $this->assertEqual($first_milestone->getDueOn()->toMySQL(), $this->friday->toMySQL());

      $second_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'Second milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $next_monday,
        'due_on' => $next_monday,
        'state' => STATE_VISIBLE,
      ), true);

      $this->assertEqual($second_milestone->getStartOn()->toMySQL(), $next_monday);
      $this->assertEqual($second_milestone->getDueOn()->toMySQL(), $next_monday);

      $this->assertTrue($second_milestone->isLoaded());

      $third_milestone = ProjectObjects::create('Milestone', array(
        'name' => 'Third milestone',
        'project_id' => $this->active_project->getId(),
        'start_on' => $the_other_monday,
        'due_on' => $the_other_monday,
        'state' => STATE_VISIBLE,
      ), true);

      $this->assertEqual($third_milestone->getStartOn()->toMySQL(), $the_other_monday);
      $this->assertEqual($third_milestone->getDueOn()->toMySQL(), $the_other_monday);

      $this->assertTrue($third_milestone->isLoaded());

      if($first_milestone instanceof Milestone && $second_milestone instanceof Milestone && $third_milestone instanceof Milestone) {
        ProjectScheduler::rescheduleMilestone($first_milestone, $next_monday, $next_monday, true, array(&$second_milestone, &$third_milestone));

        $this->assertEqual($first_milestone->getStartOn()->toMySQL(), $next_monday->toMySQL());
        $this->assertEqual($first_milestone->getDueOn()->toMySQL(), $next_monday->toMySQL());

        $this->assertEqual($second_milestone->getStartOn()->toMySQL(), $next_tuesday->toMySQL());
        $this->assertEqual($second_milestone->getDueOn()->toMySQL(), $next_tuesday->toMySQL());

        $this->assertEqual($third_milestone->getStartOn()->toMySQL(), $the_other_tuesday->toMySQL());
        $this->assertEqual($third_milestone->getDueOn()->toMySQL(), $the_other_tuesday->toMySQL());
      } // if
    } // testWeekendSkipping

    // ---------------------------------------------------
    //  / New Milestone Reschedule Tests
    // ---------------------------------------------------

    /**
     * Test reschedule project
     */
    function testRescheduleProject() {
      $this->assertEqual($this->active_milestone->getStartOn()->toMySQL(), $this->active_milestone->getDueOn()->toMySQL(), 'Single day milestone');

      ProjectScheduler::rescheduleProject($this->active_project, $this->monday, $this->tuesday);

      $this->active_milestone = Milestones::findById($this->active_milestone->getId()); // Reload

      $this->assertEqual($this->active_milestone->getStartOn()->toMySQL(), $this->tuesday->toMySQL(), 'Milestone start date has been moved');
      $this->assertEqual($this->active_milestone->getDueOn()->toMySQL(), $this->tuesday->toMySQL(), 'Milestone due date has been moved');

      $this->active_task = Tasks::findById($this->active_task->getId()); // Reload

      $this->assertEqual($this->active_task->getDueOn()->toMySQL(), $this->wednesday->toMySQL(), 'Task has been rescheduled');
      $this->assertEqual($this->active_task->subtasks()->get($this->logged_user)->getRowAt(0)->getDueOn()->toMySQL(), $this->thursday->toMySQL(), 'Subtask has been rescheduled');
    } // testRescheduleProject
    
  }