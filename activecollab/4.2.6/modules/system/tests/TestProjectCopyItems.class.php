<?php

  /**
   * Test copy project items
   *
   * @package activeCollab.modules.system
   * @subpackage tests
   */
  class TestProjectCopyItems extends AngieModelTestCase {
  
    private $logged_user;
    
    private $second_user;
    
    private $source_project;
    
    private $active_milestone;
    
    private $general_tasks_category;
    
    private $task_with_category_and_milestone;
    
    private $task_with_category;
    
    private $task_without_category_and_milestone;
    
    private $general_discussions_category;
    
    private $discussion_with_category_and_milestone;
    
    private $discussion_without_category_and_milestone;

    function setUp() {
      parent::setUp();
      
      $this->logged_user = Users::findById(1);
      
      $this->second_user = new Administrator();
      $this->second_user->setAttributes(array(
        'email' => 'second-user@test.com', 
        'company_id' => 1, 
        'password' => 'test',
      ));
      $this->second_user->setState(STATE_VISIBLE);
      $this->second_user->save();
      
      $this->source_project = new Project();
      $this->source_project->setAttributes(array(
        'name' => 'Source', 
        'leader_id' => 1, 
        'company_id' => 1, 
      ));
      $this->source_project->save();
      
      $this->source_project->users()->add($this->logged_user);
      $this->source_project->users()->add($this->second_user);
      
      $this->target_project = new Project();
      $this->target_project->setAttributes(array(
        'name' => 'Target', 
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
      
      $this->general_tasks_category = new TaskCategory();
      $this->general_tasks_category->setParent($this->source_project);
      $this->general_tasks_category->setName('General Tasks');
      $this->general_tasks_category->save();
      
      $this->task_with_category_and_milestone = new Task();
      $this->task_with_category_and_milestone->setName('Task with category and milestone');
      $this->task_with_category_and_milestone->setProject($this->source_project);
      $this->task_with_category_and_milestone->setMilestone($this->active_milestone);
      $this->task_with_category_and_milestone->category()->set($this->general_tasks_category);
      $this->task_with_category_and_milestone->setCreatedBy($this->logged_user);
      $this->task_with_category_and_milestone->setState(STATE_VISIBLE);
      $this->task_with_category_and_milestone->setVisibility(VISIBILITY_NORMAL);
      $this->task_with_category_and_milestone->save();
      
      // Set up task assignees
      $this->task_with_category_and_milestone->assignees()->setAssignee($this->logged_user);
      $this->task_with_category_and_milestone->assignees()->setOtherAssignees(array($this->second_user));
      
      // Set up subscribers
      $this->task_with_category_and_milestone->subscriptions()->subscribe($this->second_user);
      
      $this->task_with_category_and_milestone->comments()->submit('Comment without attachments', $this->logged_user, array(
        'notify_subscribers' => false, 
      ));
      
      $comment = $this->task_with_category_and_milestone->comments()->submit('Comment text', $this->logged_user, array(
        'notify_subscribers' => false, 
      ));
      
      // Set up subtasks
      $subtask = $this->task_with_category_and_milestone->subtasks()->newSubtask();
      
      $subtask->setAttributes(array(
        'body' => 'Subtask text', 
      ));
      $subtask->setCreatedBy($this->logged_user);
      $subtask->setState(STATE_VISIBLE);
      $subtask->save();
      
      $subtask->subscriptions()->subscribe($this->second_user);
      
      // Task with category only
      $this->task_with_category = new Task();
      $this->task_with_category->setName('Task with category');
      $this->task_with_category->setProject($this->source_project);
      $this->task_with_category->setCategoryId($this->general_tasks_category->getId());
      $this->task_with_category->setCreatedBy($this->logged_user);
      $this->task_with_category->setState(STATE_VISIBLE);
      $this->task_with_category->setVisibility(VISIBILITY_NORMAL);
      $this->task_with_category->save();
      
      // Task without category and milestone
      $this->task_without_category_and_milestone = new Task();
      $this->task_without_category_and_milestone->setName('Task without category and milestone');
      $this->task_without_category_and_milestone->setProject($this->source_project);
      $this->task_without_category_and_milestone->setCreatedBy($this->logged_user);
      $this->task_without_category_and_milestone->setState(STATE_VISIBLE);
      $this->task_without_category_and_milestone->setVisibility(VISIBILITY_NORMAL);
      $this->task_without_category_and_milestone->save();
      
      // General discussions category
      $this->general_discussions_category = new DiscussionCategory();
      $this->general_discussions_category->setParent($this->source_project);
      $this->general_discussions_category->setName('General Discussions');
      $this->general_discussions_category->save();
      
      // Discussion with category and milestone
      $this->discussion_with_category_and_milestone = new Discussion();
      $this->discussion_with_category_and_milestone->setAttributes(array(
        'name' => 'Discussion with category and milestone', 
        'body' => 'Discussion with category and milestone body'
      ));
      $this->discussion_with_category_and_milestone->setProject($this->source_project);
      $this->discussion_with_category_and_milestone->setMilestone($this->active_milestone);
      $this->discussion_with_category_and_milestone->category()->set($this->general_discussions_category);
      $this->discussion_with_category_and_milestone->setCreatedBy($this->logged_user);
      $this->discussion_with_category_and_milestone->setState(STATE_VISIBLE);
      $this->discussion_with_category_and_milestone->setVisibility(VISIBILITY_NORMAL);
      $this->discussion_with_category_and_milestone->save();
      
      // Discussion without category and milestone
      $this->discussion_without_category_and_milestone = new Discussion();
      $this->discussion_without_category_and_milestone->setAttributes(array(
        'name' => 'Discussion without category and milestone', 
        'body' => 'Discussion without category and milestone body'
      ));
      $this->discussion_without_category_and_milestone->setProject($this->source_project);
      $this->discussion_without_category_and_milestone->setCreatedBy($this->logged_user);
      $this->discussion_without_category_and_milestone->setState(STATE_VISIBLE);
      $this->discussion_without_category_and_milestone->setVisibility(VISIBILITY_NORMAL);
      $this->discussion_without_category_and_milestone->save();
    } // setUp
    
    function tearDown() {
      $this->logged_user = null;
      $this->second_user = null;
      $this->source_project = null;
      $this->target_project = null;
      $this->active_milestone = null;
      $this->general_tasks_category = null;
      $this->task_with_category_and_milestone = null;
      $this->task_with_category = null;
      $this->task_without_category_and_milestone = null;
      $this->general_discussions_category = null;
      $this->discussion_with_category_and_milestone = null;
      $this->discussion_without_category_and_milestone = null;
    } // tearDown
    
    function testInitialization() {
      $this->assertTrue($this->logged_user->isLoaded(), 'Logged user loaded');
      $this->assertTrue($this->second_user->isLoaded(), 'Second user loaded');
      $this->assertTrue($this->source_project->isLoaded(), 'Source project is created');
      $this->assertTrue($this->target_project->isLoaded(), 'Target project is created');
      
      $this->assertTrue($this->source_project->users()->isMember($this->logged_user, false), 'Logged user is project member');
      $this->assertTrue($this->source_project->users()->isLeader($this->logged_user), 'Logged user is project leader');
      $this->assertTrue($this->source_project->users()->isMember($this->second_user, false), 'Second user is project member');
      
      $this->assertTrue($this->active_milestone->isLoaded(), 'Test milestone is created');
      $this->assertEqual(ApplicationObjects::getRememberedContext($this->active_milestone), ApplicationObjects::getContext($this->source_project) . '/milestones/' . $this->active_milestone->getId(), 'Test milestone context is OK');
      
      $this->assertTrue($this->general_tasks_category->isLoaded(), 'General tasks category is loaded');
      
      // Task with milestone and category
      $this->assertTrue($this->task_with_category_and_milestone->isLoaded(), 'Task with category and milestone is created');
      $this->assertEqual($this->task_with_category_and_milestone->getTaskId(), 1, 'Task with category and milestone is task #1');
      $this->assertEqual($this->task_with_category_and_milestone->getCategoryId(), $this->general_tasks_category->getId(), 'Task category is set');
      $this->assertEqual($this->task_with_category_and_milestone->getMilestoneId(), $this->active_milestone->getId(), 'Task milestone is set');
      
      // Test task assignees initialization
      $this->assertEqual($this->task_with_category_and_milestone->getAssigneeId(), $this->logged_user->getId(), 'Assignee is properly set');
      
      $other_assignees = $this->task_with_category_and_milestone->assignees()->getOtherAssigneeIds(false);
      $this->assertTrue(is_array($other_assignees), 'Other assignees is an array of assignees');
      $this->assertEqual(count($other_assignees), 1, 'One other assignee');
      $this->assertEqual($other_assignees[0], $this->second_user->getId(), 'Other assignee is second user');
      
      // Test subscribers initialization
      $this->assertTrue($this->task_with_category_and_milestone->subscriptions()->isSubscribed($this->second_user, false));
      
      // Test task comments initialization
      $this->assertEqual($this->task_with_category_and_milestone->comments()->count($this->logged_user), 2, '2 comments attached to the task');
      
      // Test task subtasks
      $this->assertTrue($this->task_with_category_and_milestone->subtasks()->count($this->logged_user), 'One subtask added to the task');
      $this->assertIsA($this->task_with_category_and_milestone->subtasks()->get($this->logged_user), 'DBResult', 'Correct response for get subtasks');
      $this->assertTrue($this->task_with_category_and_milestone->subtasks()->get($this->logged_user)->getRowAt(0)->subscriptions()->isSubscribed($this->second_user, false), 'Second user subscribed to subtask');
      
      // Task with category
      $this->assertTrue($this->task_with_category->isLoaded(), 'Task with category is loaded');
      $this->assertEqual($this->task_with_category->getTaskId(), 2, 'Task with category is task #2');
      $this->assertEqual($this->task_with_category->getCategoryId(), $this->general_tasks_category->getId(), 'Category is set for task #2');
      $this->assertNull($this->task_with_category->getMilestoneId(), 'Task milestone is NULL');
      
      // Task without milestone and category
      $this->assertTrue($this->task_without_category_and_milestone->isLoaded(), 'Task without category and milestone is loaded');
      $this->assertEqual($this->task_without_category_and_milestone->getTaskId(), 3, 'Task without category and milestone is task #3');
      $this->assertNull($this->task_without_category_and_milestone->getCategoryId(), 'Task category is NULL');
      $this->assertNull($this->task_without_category_and_milestone->getMilestoneId(), 'Task milestone is NULL');
      
      // General discussion category
      $this->assertTrue($this->general_discussions_category->isLoaded(), 'General discussions category is loaded');
      
      // Discussion with category and milestone
      $this->assertTrue($this->discussion_with_category_and_milestone->isLoaded(), 'Discussion with category and milestone is created');
      $this->assertEqual($this->discussion_with_category_and_milestone->getCategoryId(), $this->general_discussions_category->getId(), 'Discussion category is set');
      $this->assertEqual($this->discussion_with_category_and_milestone->getMilestoneId(), $this->active_milestone->getId(), 'Discussion milestone is set');
      
      // Discussion without category and milestone
      $this->assertTrue($this->discussion_without_category_and_milestone->isLoaded(), 'Discussion without category and milestone is loaded');
      $this->assertNull($this->discussion_without_category_and_milestone->getCategoryId(), 'Discussion category is NULL');
      $this->assertNull($this->discussion_without_category_and_milestone->getMilestoneId(), 'Discussion milestone is NULL');
    } // testInitialization
    
    function testCopyItems() {
      $this->source_project->copyItems($this->target_project);
      
      $this->assertTrue($this->target_project->users()->isMember($this->logged_user, false), 'Logged user is project member');
      $this->assertTrue($this->target_project->users()->isLeader($this->logged_user), 'Logged user is project leader');
      $this->assertTrue($this->target_project->users()->isMember($this->second_user, false), 'Second user is project member');
      
      // Get and verify that milestones are properly copied
      $milestones = Milestones::findByProject($this->target_project, $this->logged_user);
      
      $this->assertIsA($milestones, 'DBResult', 'We have milestones');
      $this->assertEqual($milestones->count(), 1, 'One milestone in result');
      
      $copied_milestone = $milestones->getRowAt(0);
      
      $this->assertIsA($copied_milestone, 'Milestone', 'We have a valid milestone');
      $this->assertEqual($copied_milestone->getName(), $this->active_milestone->getName(), 'Same milestone name');
      $this->assertNotEqual($copied_milestone->getId(), $this->active_milestone->getId(), 'Milestone IDs should be different');
      
      // Get and verify that task categories are properly copied
      $task_categories = Categories::findBy($this->target_project, 'TaskCategory');
      
      $this->assertIsA($task_categories, 'DBResult', 'We have categories');
      $this->assertEqual($task_categories->count(), 1, 'One category in result');
      
      $copied_category = $task_categories->getRowAt(0);
      
      $this->assertIsA($copied_category, 'TaskCategory', 'We have a valid task category');
      $this->assertEqual($copied_category->getName(), $this->general_tasks_category->getName(), 'Same task category name');
      $this->assertNotEqual($copied_category->getId(), $this->general_tasks_category->getId(), 'Category IDs should be different');
      
      // Get and verify that tasks are properly copied
      $tasks = Tasks::findByProject($this->target_project, $this->logged_user);
      
      $this->assertIsA($tasks, 'DBResult', 'Target project has tasks copied to it');
      $this->assertEqual($tasks->count(), 3, 'Two tasks are copied to target project');
      
      foreach($tasks as $task) {
        if($task->getTaskId() == 1) {
          $this->assertEqual($task->getName(), 'Task with category and milestone', 'Valid task #1 name');
          $this->assertEqual($task->getCategoryId(), $copied_category->getId(), 'Moved to proper category');
          $this->assertEqual($task->getMilestoneId(), $copied_milestone->getId(), 'Moved to proper milestone');
        } elseif($task->getTaskId() == 2) {
          $this->assertEqual($task->getName(), 'Task with category', 'Valid task #3 name');
          $this->assertEqual($task->getCategoryId(), $copied_category->getId(), 'Moved to proper category');
          $this->assertEqual($task->getMilestoneId(), 0, 'No milestone for task #3');
        } elseif($task->getTaskId() == 3) {
          $this->assertEqual($task->getName(), 'Task without category and milestone', 'Valid task #3 name');
          $this->assertEqual($task->getCategoryId(), 0, 'No category for task #3');
          $this->assertEqual($task->getMilestoneId(), 0, 'No milestone for task #3');
        } else {
          $this->fail('Only task #1, #2 and #3 should be copied to target project. Task #' . $task->getTaskId() . ' found');
        } // if
      } // foreach
      
      $discussions = Discussions::findByProject($this->target_project);
    } // testCopyItems
    
  }