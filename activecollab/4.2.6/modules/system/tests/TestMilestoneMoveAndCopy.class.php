<?php

  /**
   * Test milestone move and copy
   *
   * @package activeCollab.modules.system
   * @subpackage tests
   */
  class TestMilestoneMoveAndCopy extends AngieModelTestCase {

    /**
     * Logged user
     *
     * @var User
     */
    private $logged_user;

    /**
     * Second user instance
     *
     * @var User
     */
    private $second_user;

    /**
     * Souce project
     *
     * @var Project
     */
    private $source_project;

    /**
     * Target project
     *
     * @var Project
     */
    private $target_project;

    /**
     * Selected milestone
     *
     * @var Milestone
     */
    private $active_milestone;

    /**
     * Selected task
     *
     * @var Task
     */
    private $active_task;

    /**
     * Selected discussion
     *
     * @var Discussion
     */
    private $active_discussion;

    /**
     * Selected notebook
     *
     * @var Notebook
     */
    private $active_notebook;

    function setUp() {
      parent::setUp();
      
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
      
      $this->source_project = new Project();
      $this->source_project->setAttributes(array(
        'name' => 'Source Project', 
        'leader_id' => 1, 
        'company_id' => 1, 
      ));
      $this->source_project->save();
      
      $this->target_project = new Project();
      $this->target_project->setAttributes(array(
        'name' => 'Target Project', 
        'leader_id' => 1, 
        'company_id' => 1, 
      ));
      $this->target_project->save();
      
      $this->active_milestone = new Milestone();
      $this->active_milestone->setAttributes(array(
        'name' => 'Test Subject', 
      ));
      $this->active_milestone->setProject($this->source_project);
      $this->active_milestone->setCreatedBy($this->logged_user);
      $this->active_milestone->setState(STATE_VISIBLE);
      $this->active_milestone->save();
      
      $this->active_task = new Task();
      $this->active_task->setName('Test task');
      $this->active_task->setProject($this->source_project);
      $this->active_task->setMilestone($this->active_milestone);
      $this->active_task->setCreatedBy($this->logged_user);
      $this->active_task->setState(STATE_VISIBLE);
      $this->active_task->setVisibility(VISIBILITY_NORMAL);
      $this->active_task->save();
      
      $this->active_discussion = new Discussion();
      $this->active_discussion->setAttributes(array(
        'name' => 'Test discussion', 
        'body' => 'Testing milestone copy and move'
      ));
      $this->active_discussion->setProject($this->source_project);
      $this->active_discussion->setMilestone($this->active_milestone);
      $this->active_discussion->setCreatedBy($this->logged_user);
      $this->active_discussion->setState(STATE_VISIBLE);
      $this->active_discussion->setVisibility(VISIBILITY_NORMAL);
      $this->active_discussion->save();
      
      $this->active_notebook = new Notebook();
      $this->active_notebook->setName('Test notebook');
      $this->active_notebook->setProject($this->source_project);
      $this->active_notebook->setMilestone($this->active_milestone);
      $this->active_notebook->setCreatedBy($this->logged_user);
      $this->active_notebook->setState(STATE_VISIBLE);
      $this->active_notebook->setVisibility(VISIBILITY_NORMAL);
      $this->active_notebook->save();
    } // setUp
    
    function tearDown() {
      $this->logged_user = null;
      $this->second_user = null;
      $this->source_project = null;
      $this->target_project = null;
      $this->active_milestone = null;
      $this->active_task = null;
      $this->active_discussion = null;
      $this->active_notebook = null;
    } // tearDown
    
    function testInitialization() {
      $this->assertTrue($this->logged_user->isLoaded(), 'Logged user loaded');
      $this->assertTrue($this->second_user->isLoaded(), 'Second user loaded');
      $this->assertTrue($this->source_project->isLoaded(), 'Source project is created');
      $this->assertTrue($this->target_project->isLoaded(), 'Target project is created');
      
      $this->assertTrue($this->active_milestone->isLoaded(), 'Test milestone is created');
      $this->assertEqual(ApplicationObjects::getRememberedContext($this->active_milestone), ApplicationObjects::getContext($this->source_project) . '/milestones/' . $this->active_milestone->getId(), 'Test milestone context is OK');
      
      $this->assertTrue($this->active_task->isLoaded(), 'Test task is created');
      $this->assertEqual(ApplicationObjects::getRememberedContext($this->active_task), ApplicationObjects::getContext($this->source_project) . '/tasks/normal/' . $this->active_task->getId(), 'Test task context is OK');
      
      $this->assertTrue($this->active_discussion->isLoaded(), 'Test discussions is created');
      $this->assertEqual(ApplicationObjects::getRememberedContext($this->active_discussion), ApplicationObjects::getContext($this->source_project) . '/discussions/normal/' . $this->active_discussion->getId(), 'Test discussion context is OK');
      
      $this->assertTrue($this->active_notebook->isLoaded(), 'Test notebook is created');
      $this->assertEqual(ApplicationObjects::getRememberedContext($this->active_notebook), ApplicationObjects::getContext($this->source_project) . '/notebooks/normal/' . $this->active_notebook->getId(), 'Test notebook context is OK');
    } // testInitialization

    /**
     * Test milestone copy
     */
    function testCopy() {
      $milestone_copy = $this->active_milestone->copyToProject($this->target_project);
      
      $this->assertIsA($milestone_copy, 'Milestone', 'We got milestone copy');
      $this->assertTrue($milestone_copy->getProjectId(), $this->target_project->getId(), 'Milestone copy is in target project');
      $this->assertNotEqual($this->active_milestone->getId(), $milestone_copy->getId(), 'We have a different milestone');
      
      $discussions = Discussions::findByMilestone($milestone_copy);
      
      $this->assertIsA($discussions, 'DBResult', 'Discussions have been copied with milestone');
      $this->assertEqual($discussions->count(), 1, 'One discussion copied');
      $this->assertIsA($discussions->getRowAt(0), 'Discussion', 'First instance is a discussion');
      $this->assertEqual($discussions->getRowAt(0)->getProjectId(), $this->target_project->getId(), 'Discussion is in target project');
      $this->assertEqual($discussions->getRowAt(0)->getMilestoneId(), $milestone_copy->getId(), 'Discussion is in copied milestone');
      
      $tasks = Tasks::findByMilestone($milestone_copy);
      
      $this->assertIsA($tasks, 'DBResult', 'Tasks have been copied with milestone');
      $this->assertEqual($tasks->count(), 1, 'One task copied');
      $this->assertIsA($tasks->getRowAt(0), 'Task', 'First instance is a task');
      $this->assertEqual($tasks->getRowAt(0)->getProjectId(), $this->target_project->getId(), 'Task is in target project');
      $this->assertEqual($tasks->getRowAt(0)->getMilestoneId(), $milestone_copy->getId(), 'Task is in copied milestone');
      
      $notebooks = Notebooks::findByMilestone($milestone_copy);
      
      $this->assertIsA($notebooks, 'DBResult', 'Notebooks have been copied with milestone');
      $this->assertEqual($notebooks->count(), 1, 'One notebook copied');
      $this->assertIsA($notebooks->getRowAt(0), 'Notebook', 'First instance is a notebook');
      $this->assertEqual($notebooks->getRowAt(0)->getProjectId(), $this->target_project->getId(), 'Notebook is in target project');
      $this->assertEqual($notebooks->getRowAt(0)->getMilestoneId(), $milestone_copy->getId(), 'Notebook is in copied milestone');
    } // testCopy

    /**
     * Test milestone move
     */
    function testMove() {
      $this->active_milestone->getViewUrl();
      
      $this->active_milestone->moveToProjectAndPreserveCategory($this->target_project);
      
      $this->assertEqual($this->active_milestone->getProjectId(), $this->target_project->getId(), 'Project ID updated');
      $this->assertEqual($this->active_milestone->getViewUrl(), Router::assemble('project_milestone', array(
        'project_slug' => $this->target_project->getSlug(), 
        'milestone_id' => $this->active_milestone->getId(), 
      )), 'Milestone URL is OK');
      $this->assertContains($this->active_milestone->getViewUrl(), $this->target_project->getSlug(), 'Milestone URL contains target project slug');
      
      $this->active_milestone->describe($this->logged_user);
      
      $discussion = Discussions::findById($this->active_discussion->getId());
      $task = Tasks::findById($this->active_task->getId());

      if($discussion instanceof Discussion) {
        $this->assertTrue($discussion->isLoaded(), 'Discussion is loaded');
        $this->assertEqual($discussion->getProjectId(), $this->target_project->getId(), 'Discussion is moved to target project');
        $this->assertEqual($discussion->getMilestoneId(), $this->active_milestone->getId(), 'Milestone is updated');
      } else {
        $this->fail('Discussion is not loaded');
      } // if

      if($task instanceof Task) {
        $this->assertTrue($task->isLoaded(), 'Task is loaded');
        $this->assertEqual($task->getProjectId(), $this->target_project->getId(), 'Task is moved to target project');
        $this->assertEqual($task->getMilestoneId(), $this->active_milestone->getId(), 'Milestone is updated');
      } else {
        $this->fail('Task is not loaded');
      } // if
    } // testMove
    
  }