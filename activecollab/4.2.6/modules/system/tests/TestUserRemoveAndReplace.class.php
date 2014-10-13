<?php

  /**
   * Test user remove / replace
   *
   * @package activeCollab.modules.system
   * @subpackage tests
   */
  class TestUserRemoveAndReplace extends AngieModelTestCase {

    /**
     * Logged user instance
     *
     * @var User
     */
    private $first_user;

    /**
     * Second user instance
     *
     * @var User
     */
    private $second_user;

    /**
     * Third user instance
     *
     * @var user
     */
    private $third_user;

    /**
     * Project used for testing
     *
     * @var Project
     */
    private $project;

    /**
     * First task instance
     *
     * @var Task
     */
    private $task1;

    /**
     * Task #1 subtask instance
     *
     * @var Subtask
     */
    private $task1_subtask;

    /**
     * Second task instance
     *
     * @var Task
     */
    private $task2;

    /**
     * Set up test case
     */
    function setUp() {
      parent::setUp();

      $this->first_user = Users::findById(1);

      $this->second_user = new Administrator();
      $this->second_user->setAttributes(array(
        'email' => 'second-user@test.com',
        'company_id' => 1,
        'password' => 'test',
        'role_id' => 1,
      ));
      $this->second_user->setState(STATE_VISIBLE);
      $this->second_user->save();

      $this->third_user = new Administrator();
      $this->third_user->setAttributes(array(
        'email' => 'third-user@test.com',
        'company_id' => 1,
        'password' => 'test',
      ));
      $this->third_user->setState(STATE_VISIBLE);
      $this->third_user->save();

      $this->project = new Project();
      $this->project->setAttributes(array(
        'name' => 'Test Project',
        'leader_id' => $this->first_user->getId(),
        'company_id' => 1,
      ));
      $this->project->save();

      $this->project->users()->add($this->first_user);
      $this->project->users()->add($this->second_user, null, array(
        'task' => ProjectRole::PERMISSION_MANAGE,
      ));

      $this->task1 = new Task();
      $this->task1->setName('Task 1');
      $this->task1->assignees()->setAssignee($this->second_user, $this->first_user, false);
      $this->task1->setProject($this->project);
      $this->task1->setCreatedBy($this->first_user);
      $this->task1->setState(STATE_VISIBLE);
      $this->task1->save();

      $this->task1->subscriptions()->subscribe($this->first_user);
      $this->task1->subscriptions()->subscribe($this->second_user);

      $this->task1_subtask = $this->task1->subtasks()->newSubtask();

      $this->task1_subtask->setAttributes(array(
        'body' => 'Subtask text',
      ));
      $this->task1_subtask->assignees()->setAssignee($this->second_user, $this->first_user, false);
      $this->task1_subtask->setCreatedBy($this->first_user);
      $this->task1_subtask->setState(STATE_VISIBLE);
      $this->task1_subtask->save();

      $this->task1_subtask->subscriptions()->subscribe($this->first_user);
      $this->task1_subtask->subscriptions()->subscribe($this->second_user);

      $this->task2 = new Task();
      $this->task2->setName('Task 2');
      $this->task2->assignees()->setAssignee($this->first_user, $this->first_user, false);
      $this->task2->setProject($this->project);
      $this->task2->setCreatedBy($this->first_user);
      $this->task2->setState(STATE_VISIBLE);
      $this->task2->save();

      $this->task2->subscriptions()->subscribe($this->first_user);
      $this->task2->subscriptions()->subscribe($this->second_user);
      $this->task2->assignees()->setOtherAssignees(array($this->second_user->getId()));
    } // setUp

    /**
     * Tear down after test run
     */
    function tearDown() {
      $this->first_user = null;
      $this->second_user = null;
      $this->project = null;
      $this->task1 = null;
      $this->task1_subtask = null;
      $this->task2 = null;
    } // tearDown

    function testInitialization() {
      $this->assertTrue($this->first_user->isLoaded(), 'First user loaded');
      $this->assertTrue($this->second_user->isLoaded(), 'Second user loaded');
      $this->assertTrue($this->third_user->isLoaded(), 'Third user loaded');
      $this->assertTrue($this->project->isLoaded(), 'Project is created');
      $this->assertTrue($this->project->users()->isMember($this->first_user, false), 'First user is member of test project');
      $this->assertTrue($this->project->users()->isMember($this->second_user, false), 'Second user successfully added to a project');
      $this->assertFalse($this->project->users()->isMember($this->third_user, false), 'Third user is not a member of test project');

      $this->assertTrue($this->task1->isLoaded(), 'Task #1 created');
      $this->assertTrue($this->task1->assignees()->isAssignee($this->second_user), 'Second user is assignee');
      $this->assertTrue($this->task1->subscriptions()->isSubscribed($this->first_user, false), 'First user is subscribed');
      $this->assertTrue($this->task1->subscriptions()->isSubscribed($this->second_user, false), 'Second user is subscribed');

      $this->assertTrue($this->task1_subtask->isLoaded(), 'Subtask in Task #1 created');
      $this->assertTrue($this->task1_subtask->assignees()->isAssignee($this->second_user), 'Second user is assignee');
      $this->assertTrue($this->task1_subtask->subscriptions()->isSubscribed($this->first_user, false), 'First user is subscribed');
      $this->assertTrue($this->task1_subtask->subscriptions()->isSubscribed($this->second_user, false), 'Second user is subscribed');

      $this->assertTrue($this->task2->isLoaded(), 'Task #2 created');
      $this->assertTrue($this->task2->assignees()->isAssignee($this->first_user), 'First user is assignee');
      $this->assertTrue($this->task2->assignees()->isAssignee($this->second_user), 'Second user is other assignee');
      $this->assertTrue($this->task2->subscriptions()->isSubscribed($this->first_user, false), 'First user is subscribed');
      $this->assertTrue($this->task2->subscriptions()->isSubscribed($this->second_user, false), 'Second user is subscribed');
    } // testInitialization

    /**
     * Test remove from a project functionality
     */
    function testRemoveFromAProject() {

      $task3 = new Task();
      $task3->setName('Task 3');
      $task3->assignees()->setAssignee($this->first_user, $this->first_user, false);
      $task3->setProject($this->project);
      $task3->setCreatedBy($this->first_user);
      $task3->setState(STATE_VISIBLE);
      $task3->save();

      $task3->subscriptions()->subscribe($this->first_user);
      $task3->subscriptions()->subscribe($this->second_user);
      $task3->subscriptions()->subscribe($this->third_user);
      $task3->assignees()->setOtherAssignees(array($this->second_user->getId(), $this->third_user->getId()));

      $this->assertTrue($task3->isLoaded(), 'Task #3 created');
      $this->assertTrue($task3->assignees()->isAssignee($this->first_user), 'First user is assignee');
      $this->assertTrue($task3->assignees()->isAssignee($this->second_user), 'Second user is other assignee');
      $this->assertTrue($task3->assignees()->isAssignee($this->third_user), 'Third user is other assignee');
      $this->assertTrue($task3->subscriptions()->isSubscribed($this->first_user, false), 'First user is subscribed');
      $this->assertTrue($task3->subscriptions()->isSubscribed($this->second_user, false), 'Second user is subscribed');
      $this->assertTrue($task3->subscriptions()->isSubscribed($this->third_user, false), 'Third user is subscribed');

      $task4 = new Task();
      $task4->setName('Task 4');
      $task4->assignees()->setAssignee($this->second_user, $this->first_user, false);
      $task4->setProject($this->project);
      $task4->setCreatedBy($this->first_user);
      $task4->setState(STATE_VISIBLE);
      $task4->save();

      $task4->subscriptions()->subscribe($this->first_user);
      $task4->subscriptions()->subscribe($this->second_user);
      $task4->subscriptions()->subscribe($this->third_user);
      $task4->assignees()->setOtherAssignees(array($this->first_user->getId(), $this->third_user->getId()));

      $this->assertTrue($task4->isLoaded(), 'Task #4 created');
      $this->assertTrue($task4->assignees()->isAssignee($this->second_user), 'Second user is assignee');
      $this->assertTrue($task4->assignees()->isAssignee($this->first_user), 'First user is other assignee');
      $this->assertTrue($task4->assignees()->isAssignee($this->third_user), 'Third user is other assignee');
      $this->assertTrue($task4->subscriptions()->isSubscribed($this->first_user, false), 'First user is subscribed');
      $this->assertTrue($task4->subscriptions()->isSubscribed($this->second_user, false), 'Second user is subscribed');
      $this->assertTrue($task4->subscriptions()->isSubscribed($this->third_user, false), 'Third user is subscribed');

      // ---------------------------------------------------
      //  Remove user from project
      // ---------------------------------------------------

      $this->project->users()->remove($this->second_user, $this->first_user);

      // Reload tasks
      $this->task1 = Tasks::findById($this->task1->getId());
      $this->task1_subtask = Subtasks::findByid($this->task1_subtask->getId());
      $this->task2 = Tasks::findById($this->task2->getId());
      $task3 = Tasks::findById($task3->getId());
      $task4 = Tasks::findById($task4->getId());

      $this->assertTrue($this->task1->isLoaded(), 'Task #1 reloaded');
      $this->assertTrue($this->task1_subtask->isLoaded(), 'Subtask on task #1 reloaded');
      $this->assertTrue($this->task2->isLoaded(), 'Task #2 reloaded');
      $this->assertTrue($task3->isLoaded(), 'Task #3 reloaded');

      $this->assertNull($this->task1->assignees()->getAssignee(), 'No assignee for first task');
      $this->assertTrue($this->task1->subscriptions()->isSubscribed($this->first_user), 'First user is still subscribed to task #1');
      $this->assertFalse($this->task1->subscriptions()->isSubscribed($this->second_user), 'Second user is no longer subscribed to task #1');

      $this->assertNull($this->task1_subtask->assignees()->getAssignee(), 'No assignee for subtask on first task');
      $this->assertTrue($this->task1_subtask->subscriptions()->isSubscribed($this->first_user), 'First user is still subscribed to subtask on task #1');
      $this->assertFalse($this->task1_subtask->subscriptions()->isSubscribed($this->second_user), 'Second user is no longer subscribed to subtask ontask #1');

      $this->assertTrue($this->task2->assignees()->isAssignee($this->first_user), 'First user is still assigned to task #2');
      $this->assertNull($this->task2->assignees()->getOtherAssigneeIds(false), 'There are no other assignees for task #2');
      $this->assertTrue($this->task2->subscriptions()->isSubscribed($this->first_user), 'First user is still subscribed to task #2');
      $this->assertFalse($this->task2->subscriptions()->isSubscribed($this->second_user), 'Second user is no longer subscribed to task #2');

      $this->assertTrue($task3->assignees()->isAssignee($this->first_user), 'First user is still assigned to task #3');
      $this->assertEqual($task3->assignees()->getOtherAssigneeIds(false), array($this->third_user->getId()), 'Third user is still assigned to task #3');
      $this->assertTrue($task3->subscriptions()->isSubscribed($this->first_user), 'First user is still subscribed to task #3');
      $this->assertFalse($task3->subscriptions()->isSubscribed($this->second_user), 'Second user is no longer subscribed to task #3');
      $this->assertTrue($task3->subscriptions()->isSubscribed($this->third_user), 'Third user is still subscribed to task #3');

      $this->assertFalse($task4->assignees()->isAssignee($this->second_user), 'Second user is no longer assigned to task #4');
      $this->assertEqual($task4->assignees()->getOtherAssigneeIds(false), null, 'There are no other people assigned to task #4');
      $this->assertFalse($task4->subscriptions()->isSubscribed($this->first_user, false), 'First user is still subscribed to task #4');
      $this->assertFalse($task4->subscriptions()->isSubscribed($this->second_user, false), 'Second user is no longer subscribed to task #4');
      $this->assertFalse($task4->subscriptions()->isSubscribed($this->third_user, false), 'Third user is still subscribed to task #4');
    } // testRemoveFromAProject

    // ---------------------------------------------------
    //  Replace
    // ---------------------------------------------------

    /**
     * Test replace on a project functionality
     */
    function testReplaceOnAProject() {
      $this->project->users()->replace($this->second_user, $this->third_user, $this->first_user);

      $this->validateProjectUserReplacement();
    } // testReplaceOnAProject

    /**
     * Test simple replacement, without assignments
     */
    function testReplacementWithoutAssignments() {
      $task3 = new Task();
      $task3->setName('Task 3');
      $task3->setProject($this->project);
      $task3->setCreatedBy($this->first_user);
      $task3->setState(STATE_VISIBLE);
      $task3->save();

      $task3->subscriptions()->subscribe($this->first_user);
      $task3->subscriptions()->subscribe($this->second_user);
      $task3->subscriptions()->subscribe($this->third_user);

      $this->assertTrue($task3->isLoaded(), 'Task #3 created');
      $this->assertNull($task3->assignees()->getAssignee(), 'No assignee');
      $this->assertTrue($task3->subscriptions()->isSubscribed($this->first_user, false), 'First user is subscribed');
      $this->assertTrue($task3->subscriptions()->isSubscribed($this->second_user, false), 'Second user is subscribed');
      $this->assertTrue($task3->subscriptions()->isSubscribed($this->third_user, false), 'Third user is subscribed');

      $this->project->users()->replace($this->second_user, $this->third_user, $this->first_user);

      $this->validateProjectUserReplacement();

      $task3 = Tasks::findById($task3->getId());

      $this->assertTrue($task3->isLoaded(), 'Task #3 reloaded');
      $this->assertNull($task3->assignees()->getAssignee(), 'Still no assignee');
      $this->assertTrue($task3->subscriptions()->isSubscribed($this->first_user, false), 'First user is still subscribed');
      $this->assertFalse($task3->subscriptions()->isSubscribed($this->second_user, false), 'Second user is no longer subscribed (removed from project)');
      $this->assertTrue($task3->subscriptions()->isSubscribed($this->third_user, false), 'Third user is still subscribed');
    } // testReplacementWithoutAssignments

    /**
     * Replace one user with another user who's already project member
     */
    function testReplaceWithExistingProjectUser() {
      $this->project->users()->add($this->third_user, null, array(
        'task' => ProjectRole::PERMISSION_MANAGE,
      ));

      $this->assertTrue($this->project->users()->isMember($this->third_user), 'Third user added to project');

      $task3 = new Task();
      $task3->setName('Task 3');
      $task3->assignees()->setAssignee($this->third_user, $this->first_user, false);
      $task3->setProject($this->project);
      $task3->setCreatedBy($this->first_user);
      $task3->setState(STATE_VISIBLE);
      $task3->save();

      $task3->subscriptions()->subscribe($this->first_user);
      $task3->subscriptions()->subscribe($this->third_user);

      $this->assertTrue($task3->isLoaded(), 'Task #3 created');
      $this->assertTrue($task3->assignees()->isAssignee($this->third_user), 'Third user is assignee');
      $this->assertTrue($task3->subscriptions()->isSubscribed($this->first_user, false), 'First user is subscribed');
      $this->assertTrue($task3->subscriptions()->isSubscribed($this->third_user, false), 'Third user is subscribed');

      // User is being replaced with a user with whome he or she already shares some assignments / subscriptions

      $task4 = new Task();
      $task4->setName('Task 4');
      $task4->assignees()->setAssignee($this->second_user, $this->first_user, false);
      $task4->setProject($this->project);
      $task4->setCreatedBy($this->first_user);
      $task4->setState(STATE_VISIBLE);
      $task4->save();

      $task4->assignees()->setOtherAssignees(array($this->third_user));

      $task4->subscriptions()->subscribe($this->first_user);
      $task4->subscriptions()->subscribe($this->second_user);
      $task4->subscriptions()->subscribe($this->third_user);

      $this->assertTrue($task4->isLoaded(), 'Task #4 created');
      $this->assertTrue($task4->assignees()->isResponsible($this->second_user), 'Second user is responsible assignee');
      $this->assertTrue($task4->assignees()->isAssignee($this->third_user), 'Third user is other assignee');
      $this->assertTrue($task4->subscriptions()->isSubscribed($this->first_user, false), 'First user is subscribed');
      $this->assertTrue($task4->subscriptions()->isSubscribed($this->second_user, false), 'First user is subscribed');
      $this->assertTrue($task4->subscriptions()->isSubscribed($this->third_user, false), 'Third user is subscribed');

      $task4_subtask = $task4->subtasks()->newSubtask();

      $task4_subtask->setAttributes(array(
        'body' => 'Subtask text',
      ));
      $task4_subtask->assignees()->setAssignee($this->second_user, $this->first_user, false);
      $task4_subtask->setCreatedBy($this->first_user);
      $task4_subtask->setState(STATE_VISIBLE);
      $task4_subtask->save();

      $task4_subtask->subscriptions()->subscribe($this->first_user);
      $task4_subtask->subscriptions()->subscribe($this->second_user);
      $task4_subtask->subscriptions()->subscribe($this->third_user);

      $this->assertTrue($task4_subtask->isLoaded(), 'Subtask on task #4 created');
      $this->assertTrue($task4_subtask->assignees()->isResponsible($this->second_user), 'Second user is responsible assignee');
      $this->assertTrue($task4_subtask->subscriptions()->isSubscribed($this->first_user, false), 'First user is subscribed');
      $this->assertTrue($task4_subtask->subscriptions()->isSubscribed($this->second_user, false), 'First user is subscribed');
      $this->assertTrue($task4_subtask->subscriptions()->isSubscribed($this->third_user, false), 'Third user is subscribed');

      $task5 = new Task();
      $task5->setName('Task 5');
      $task5->assignees()->setAssignee($this->first_user, $this->first_user, false);
      $task5->setProject($this->project);
      $task5->setCreatedBy($this->first_user);
      $task5->setState(STATE_VISIBLE);
      $task5->save();

      $task5->assignees()->setOtherAssignees(array($this->second_user, $this->third_user));

      $task5->subscriptions()->subscribe($this->first_user);
      $task5->subscriptions()->subscribe($this->second_user);
      $task5->subscriptions()->subscribe($this->third_user);

      $this->assertTrue($task5->isLoaded(), 'Task #4 created');
      $this->assertTrue($task5->assignees()->isResponsible($this->first_user), 'First user is responsible assignee');
      $this->assertTrue($task5->assignees()->isAssignee($this->second_user), 'Second user is other assignee');
      $this->assertTrue($task5->assignees()->isAssignee($this->third_user), 'Third user is other assignee');
      $this->assertTrue($task5->subscriptions()->isSubscribed($this->first_user, false), 'First user is subscribed');
      $this->assertTrue($task5->subscriptions()->isSubscribed($this->second_user, false), 'First user is subscribed');
      $this->assertTrue($task5->subscriptions()->isSubscribed($this->third_user, false), 'Third user is subscribed');

      // Replacement is already responsible, while replaced user is assigned
      $task6 = new Task();
      $task6->setName('Task 6');
      $task6->assignees()->setAssignee($this->third_user, $this->first_user, false);
      $task6->setProject($this->project);
      $task6->setCreatedBy($this->first_user);
      $task6->setState(STATE_VISIBLE);
      $task6->save();

      $task6->assignees()->setOtherAssignees(array($this->second_user));

      $task6->subscriptions()->subscribe($this->first_user);
      $task6->subscriptions()->subscribe($this->second_user);
      $task6->subscriptions()->subscribe($this->third_user);

      $this->assertTrue($task6->isLoaded(), 'Task #6 created');
      $this->assertFalse($task6->assignees()->isAssignee($this->first_user), 'First user is not assigned');
      $this->assertTrue($task6->assignees()->isAssignee($this->second_user), 'Second user is other assignee');
      $this->assertTrue($task6->assignees()->isResponsible($this->third_user), 'Third user is responsible assignee');
      $this->assertTrue($task6->subscriptions()->isSubscribed($this->first_user, false), 'First user is subscribed');
      $this->assertTrue($task6->subscriptions()->isSubscribed($this->second_user, false), 'First user is subscribed');
      $this->assertTrue($task6->subscriptions()->isSubscribed($this->third_user, false), 'Third user is subscribed');

      // Replace with user who's already on a project
      $this->project->users()->replace($this->second_user, $this->third_user, $this->first_user);

      $this->validateProjectUserReplacement();

      $task3 = Tasks::findById($task3->getId());
      $task4 = Tasks::findById($task4->getId());
      $task4_subtask = Subtasks::findById($task4_subtask->getId());
      $task5 = Tasks::findById($task5->getId());
      $task6 = Tasks::findById($task6->getId());

      $this->assertTrue($task3->isLoaded(), 'Task #3 reloaded');
      $this->assertTrue($task3->assignees()->isAssignee($this->third_user), 'Third user remained assigned to task #3');
      $this->assertTrue($task3->subscriptions()->isSubscribed($this->third_user), 'Third user remained subscribed to task #3');

      $this->assertTrue($task4->isLoaded(), 'Task #4 reloaded');
      $this->assertTrue($task4->assignees()->isAssignee($this->third_user), 'Third user remained assigned to task #4');
      $this->assertNull($task4->assignees()->getOtherAssigneeIds(false), 'Nobody else is assigned to task #4');
      $this->assertTrue($task4->subscriptions()->isSubscribed($this->third_user, false), 'Third user remained subscribed to task #4');

      $this->assertTrue($task4_subtask->isLoaded(), 'Task #4 subtask reloaded');
      $this->assertTrue($task4_subtask->assignees()->isResponsible($this->third_user), 'Third user is assigned to task #4 subtask');
      $this->assertTrue($task4_subtask->subscriptions()->isSubscribed($this->third_user, false), 'Third user is subscribed to task #4 subtask');

      $this->assertTrue($task5->assignees()->isResponsible($this->first_user), 'First user is still responsible');
      $this->assertFalse($task5->assignees()->isAssignee($this->second_user), 'Second user is no longer assigned becuase he is no longer on the project');
      $this->assertTrue($task5->assignees()->isAssignee($this->third_user), 'Third user is still assigned');
      $this->assertTrue($task5->subscriptions()->isSubscribed($this->first_user), 'First user is subscribed');
      $this->assertFalse($task5->subscriptions()->isSubscribed($this->second_user), 'Second user is no longer subscribed');
      $this->assertTrue($task5->subscriptions()->isSubscribed($this->third_user), 'Third user is subscribed');

      $this->assertTrue($task6->assignees()->isResponsible($this->third_user), 'Third user remains responsible');
      $this->assertFalse($task6->assignees()->isAssignee($this->second_user), 'Second user is no longer assigned becuase he is no longer on the project');
      $this->assertFalse($task6->assignees()->isAssignee($this->first_user), 'Not assigned nor responsible');
      $this->assertTrue($task6->subscriptions()->isSubscribed($this->first_user), 'First user is subscribed');
      $this->assertFalse($task6->subscriptions()->isSubscribed($this->second_user), 'Second user is no longer subscribed');
      $this->assertTrue($task6->subscriptions()->isSubscribed($this->third_user), 'Third user is subscribed');
    } // testReplaceWithExistingProjectUser

    /**
     * Validate if second_user is properly replace with third_user on a project
     */
    private function validateProjectUserReplacement() {
      $this->task1 = Tasks::findById($this->task1->getId());
      $this->task1_subtask = Subtasks::findByid($this->task1_subtask->getId());
      $this->task2 = Tasks::findById($this->task2->getId());

      $this->assertFalse($this->project->users()->isMember($this->second_user), 'Second user is no longer project member');
      $this->assertTrue($this->project->users()->isMember($this->third_user), 'Third user is now project member');

      $this->assertFalse($this->task1->assignees()->isAssignee($this->second_user), 'Second user is no longer assigned to task #1');
      $this->assertFalse($this->task1_subtask->assignees()->isAssignee($this->second_user), 'Second user is no longer assigned to subtask in task #1');
      $this->assertFalse($this->task2->assignees()->isAssignee($this->second_user), 'Second user is no longer assigned to task #2');

      $this->assertFalse($this->task1->subscriptions()->isSubscribed($this->second_user), 'Second user is no longer subscribed to task #1');
      $this->assertFalse($this->task1_subtask->subscriptions()->isSubscribed($this->second_user), 'Second user is no longer subscribed to subtask in task #1');
      $this->assertFalse($this->task2->subscriptions()->isSubscribed($this->second_user), 'Second user is no longer subscribed to task #2');

      $this->assertTrue($this->task1->assignees()->isAssignee($this->third_user), 'Third user is now responsible for task #1');
      $this->assertTrue($this->task1_subtask->assignees()->isAssignee($this->third_user), 'Third user is now subtask assignee');
      $this->assertTrue($this->task2->assignees()->isAssignee($this->third_user), 'Third user is now task #2 assignee');

      $this->assertTrue($this->task1->subscriptions()->isSubscribed($this->third_user), 'Third user is now subscribed totask #1');
      $this->assertTrue($this->task1_subtask->subscriptions()->isSubscribed($this->third_user), 'Third user is now subscribed to subtask');
      $this->assertTrue($this->task2->subscriptions()->isSubscribed($this->third_user), 'Third user is now subscribed to task #2');
    } // validateProjectUserReplacement

  }