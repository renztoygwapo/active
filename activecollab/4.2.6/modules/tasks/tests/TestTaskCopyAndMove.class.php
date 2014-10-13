<?php

  /**
   * Test task copy and move
   */
  class TestTaskCopyAndMove extends AngieModelTestCase {

    /**
     * Logged user
     *
     * @var User
     */
    private $logged_user;

    /**
     * @var User
     */
    private $second_user;

    /**
     * @var Project
     */
    private $source_project;

    /**
     * @var Category
     */
    private $source_project_general_tasks;

    /**
     * @var Project
     */
    private $target_project;

    /**
     * @var Category
     */
    private $target_project_general_tasks;

    /**
     * @var Milestone
     */
    private $source_milestone;

    /**
     * @var Task
     */
    private $active_task;

    /**
     * @var Task
     */
    private $target_project_task;

    /**
     * @var string
     */
    private $test_file_path;
    
    function __construct($label = false) {
      parent::__construct($label);
      
      $this->test_file_path = ATTACHMENTS_FRAMEWORK_PATH . '/tests/resources/test.jpg';
    } // __construct

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

      $this->source_project_general_tasks = new TaskCategory();
      $this->source_project_general_tasks->setParent($this->source_project);
      $this->source_project_general_tasks->setName('General Tasks');
      $this->source_project_general_tasks->save();
      
      $this->target_project = new Project();
      $this->target_project->setAttributes(array(
        'name' => 'Target', 
        'leader_id' => 1, 
        'company_id' => 1, 
      ));
      $this->target_project->save();
      $this->target_project->users()->add($this->logged_user);
      $this->target_project->users()->add($this->second_user);
      
      $this->target_project_general_tasks = new TaskCategory();
      $this->target_project_general_tasks->setParent($this->target_project);
      $this->target_project_general_tasks->setName('General Tasks');
      $this->target_project_general_tasks->save();
      
      $this->source_milestone = new Milestone();
      $this->source_milestone->setAttributes(array(
        'name' => 'Test Subject', 
      ));
      $this->source_milestone->setProject($this->source_project);
      $this->source_milestone->setCreatedBy($this->logged_user);
      $this->source_milestone->setState(STATE_VISIBLE);
      $this->source_milestone->save();
      
      $this->active_task = new Task();
      $this->active_task->setName('Test task');
      $this->active_task->setProject($this->source_project);
      $this->active_task->setMilestone($this->source_milestone);
      $this->active_task->category()->set($this->source_project_general_tasks);
      $this->active_task->setCreatedBy($this->logged_user);
      $this->active_task->setState(STATE_VISIBLE);
      $this->active_task->setVisibility(VISIBILITY_NORMAL);
      $this->active_task->save();
      
      $this->target_project_task = new Task();
      $this->target_project_task->setName('Test task');
      $this->target_project_task->setProject($this->target_project);
      $this->target_project_task->setCreatedBy($this->logged_user);
      $this->target_project_task->setState(STATE_VISIBLE);
      $this->target_project_task->setVisibility(VISIBILITY_NORMAL);
      $this->target_project_task->save();
      
      // Set up task assignees
      $this->active_task->assignees()->setAssignee($this->logged_user);
      $this->active_task->assignees()->setOtherAssignees(array($this->second_user));
      
      // Set up subscribers
      $this->active_task->subscriptions()->subscribe($this->second_user);
      
      // Set up task attachments
      $this->attachFileAndAssertAttachmentProperties($this->active_task);
      
      $this->active_task->comments()->submit('Comment without attachments', $this->logged_user, array(
        'notify_subscribers' => false, 
      ));
      
      $comment = $this->active_task->comments()->submit('Comment text', $this->logged_user, array(
        'notify_subscribers' => false, 
      ));
      
      $this->attachFileAndAssertAttachmentProperties($comment);
      
      // Set up subtasks
      $subtask = $this->active_task->subtasks()->newSubtask();
      
      $subtask->setAttributes(array(
        'body' => 'Subtask text', 
      ));
      $subtask->setCreatedBy($this->logged_user);
      $subtask->setState(STATE_VISIBLE);
      $subtask->save();
      
      $subtask->subscriptions()->subscribe($this->second_user);
    } // setUp
    
    function tearDown() {
      $this->logged_user = null;
      $this->second_user = null;
      $this->source_project = null;
      $this->source_project_general_tasks = null;
      $this->target_project = null;
      $this->target_project_general_tasks = null;
      $this->source_milestone = null;
      $this->active_task = null;
      $this->target_project_task = null;
    } // tearDown
    
    function testInitialization() {
      $this->assertTrue($this->logged_user->isLoaded(), 'Logged user loaded');
      $this->assertTrue($this->second_user->isLoaded(), 'Second user loaded');
      $this->assertTrue($this->source_project->isLoaded(), 'Source project is created');
      $this->assertTrue($this->target_project->isLoaded(), 'Target project is created');

      $this->assertTrue(is_foreachable($this->source_project->users()->getIds()), 'We have users in source project');
      
      $this->assertTrue($this->source_project_general_tasks->isLoaded(), 'General Tasks in source project is loaded');
      $this->assertEqual($this->source_project_general_tasks->getName(), 'General Tasks', 'Name is OK');
      $this->assertTrue($this->source_project_general_tasks->getParent()->is($this->source_project), 'Source project is set as parent for General Tasks category');
      
      $this->assertTrue($this->target_project_general_tasks->isLoaded(), 'General Tasks in target project is loaded');
      $this->assertEqual($this->target_project_general_tasks->getName(), 'General Tasks', 'Name is OK');
      $this->assertTrue($this->target_project_general_tasks->getParent()->is($this->target_project), 'Target project is set as parent for General Tasks category');
      
      $this->assertTrue($this->source_milestone->isLoaded(), 'Test milestone is created');
      $this->assertEqual(ApplicationObjects::getRememberedContext($this->source_milestone), ApplicationObjects::getContext($this->source_project) . '/milestones/' . $this->source_milestone->getId(), 'Test milestone context is OK');
      
      $this->assertTrue($this->active_task->isLoaded(), 'Test task is created');
      $this->assertEqual(ApplicationObjects::getRememberedContext($this->active_task), ApplicationObjects::getContext($this->source_project) . '/tasks/normal/' . $this->active_task->getId(), 'Test task context is OK');
      $this->assertEqual($this->active_task->getCategoryId(), $this->source_project_general_tasks->getId(), 'Task category is set');
      
      $this->assertTrue($this->target_project_task->isLoaded(), 'Target project task is created');
      $this->assertEqual(ApplicationObjects::getRememberedContext($this->target_project_task), ApplicationObjects::getContext($this->target_project) . '/tasks/normal/' . $this->target_project_task->getId(), 'Target project task context is OK');
      $this->assertEqual($this->target_project_task->getTaskId(), 1, 'Start with task #1');
      
      // Test task assignees initialization
      $this->assertEqual($this->active_task->getAssigneeId(), $this->logged_user->getId(), 'Assignee is properly set');
      
      $other_assignees = $this->active_task->assignees()->getOtherAssigneeIds(false);
      $this->assertTrue(is_array($other_assignees), 'Other assignees is an array of assignees');
      $this->assertEqual(count($other_assignees), 1, 'One other assignee');
      $this->assertEqual($other_assignees[0], $this->second_user->getId(), 'Other assignee is second user');
      
      // Test subscribers initialization
      $this->assertTrue($this->active_task->subscriptions()->isSubscribed($this->second_user, false));
      
      // Test task attachments initialization
      
      // Test task comments initialization
      $this->assertEqual($this->active_task->comments()->count($this->logged_user), 2, '2 comments attached to the task');
      
      // Test task subtasks
      $this->assertTrue($this->active_task->subtasks()->count($this->logged_user), 'One subtask added to the task');
      $this->assertIsA($this->active_task->subtasks()->get($this->logged_user), 'DBResult', 'Correct response for get subtasks');
      $this->assertTrue($this->active_task->subtasks()->get($this->logged_user)->getRowAt(0)->subscriptions()->isSubscribed($this->second_user, false), 'Second user subscribed to subtask');
    } // testInitialization
    
    function testMove() {
      $this->assertEqual($this->active_task->getTaskId(), 1, 'Task ID is #1 in original project');
      $this->assertNotNull(ApplicationObjects::getRememberedContext($this->active_task->comments()->get($this->logged_user)->getRowAt(0)), 'First comment has a valid context in the database');
      $this->assertNotNull(ApplicationObjects::getRememberedContext($this->active_task->comments()->get($this->logged_user)->getRowAt(1)), 'Second comment has a valid context in the database');
      $this->assertNotNull(ApplicationObjects::getRememberedContext($this->active_task->subtasks()->get($this->logged_user)->getRowAt(0)), 'Subtask has a valid context in the database');
      
      $this->active_task->moveToProject($this->target_project);
      
      $this->assertEqual($this->active_task->getProjectId(), $this->target_project->getId(), 'Task is moved to a target project');
      $this->assertTrue($this->active_task->getProject()->is($this->target_project), 'getProject() returns target project');
      $this->assertEqual($this->active_task->getTaskId(), 2, 'Task ID is #2 in target project');
      
      $comments = $this->active_task->comments()->get($this->logged_user);
      
      $this->assertIsA($comments, 'DBResult', 'Valid comments result');
      $this->assertEqual($comments->count(), 2, 'Two comments found');
      $this->assertIsA($comments->getRowAt(0), 'Comment', 'We have a valid comment');
      $this->assertEqual(ApplicationObjects::getRememberedContext($comments->getRowAt(0)), ApplicationObjects::getContext($this->active_task) . '/comments/' . $comments->getRowAt(0)->getId(), 'Comment context is updated');
      
      $subtasks = $this->active_task->subtasks()->get($this->logged_user);
      
      $this->assertIsA($subtasks, 'DBResult', 'Valid subtasks result');
      $this->assertIsA($subtasks->getRowAt(0), 'Subtask', 'We have a valid subtask');
      $this->assertEqual(ApplicationObjects::getRememberedContext($subtasks->getRowAt(0)), ApplicationObjects::getContext($this->active_task) . '/subtasks/' . $subtasks->getRowAt(0)->getId(), 'Subtask context is updated');
    } // testMove
    
    function testCopy() {
      
      // Now that we have everything set, let's make a task copy
      $task_copy = $this->active_task->copyToProject($this->target_project);
      
      $this->assertIsA($task_copy, 'Task', 'We have a valid task instance');
      $this->assertNotEqual($this->active_task->getId(), $task_copy->getId(), 'Note same ID as original task');
      $this->assertEqual($task_copy->getProjectId(), $this->target_project->getId(), 'Task is moved to target project');
      $this->assertEqual($task_copy->getTaskId(), 2, 'Task ID is OK value in target project');
      
      // Test assignees
      $this->assertTrue($task_copy->assignees()->isAssignee($this->logged_user), 'Administrator is assignee');
      $this->assertTrue($task_copy->assignees()->isResponsible($this->logged_user), 'Administrator is responsible');
      $this->assertTrue($task_copy->assignees()->isAssignee($this->second_user), 'Second user is assignee');
      $this->assertFalse($task_copy->assignees()->isResponsible($this->second_user), 'But second user is not responsible');
      
      // Test subscribers
      $this->assertTrue($task_copy->subscriptions()->isSubscribed($this->second_user, false), 'Second user is subscribed to the task copy');
      
      // Test attachments
      $this->assertTrue($task_copy->attachments()->has($this->logged_user), 'Task copy has attachmnets');
      
      $attachments = $task_copy->attachments()->get($this->logged_user);
      
      $this->assertIsA($attachments, 'DBResult', 'Valid attachments result');
      $this->assertEqual($attachments->count(), 1, 'One attachment found');
      $this->assertIsA($attachments->getRowAt(0), 'Attachment', 'First row is an attachment instance');
      
      // Test comments
      $this->assertEqual($task_copy->comments()->count($this->logged_user, false), 2, 'Task copy has two comments');
      
      $comments = $task_copy->comments()->get($this->logged_user);
      
      $this->assertIsA($comments, 'DBResult', 'Comments is a valid database result');
      $this->assertEqual($comments->count(), 2, 'Two comments in database result');
      $this->assertIsA($comments->getRowAt(0), 'Comment', 'First element is a valid comment');
      $this->assertEqual($comments->getRowAt(0)->getBody(), 'Comment text', 'Comment text is correct');
      
      $this->assertEqual(ApplicationObjects::getContext($comments->getRowAt(0)), ApplicationObjects::getContext($task_copy) . '/comments/' . $comments->getRowAt(0)->getId(), 'Comment context is OK');

      $this->assertTrue($comments->getRowAt(0)->attachments()->has($this->logged_user), 'Comment has attachments');
      
      $attachments = $comments->getRowAt(0)->attachments()->get($this->logged_user);
      
      $this->assertIsA($attachments, 'DBResult', 'Valid attachments result');
      $this->assertEqual($attachments->count(), 1, 'One attachment found');
      $this->assertIsA($attachments->getRowAt(0), 'Attachment', 'First row is an attachment instance');
      
      // Test subtasks
      $subtasks = $task_copy->subtasks()->get($this->logged_user);
      
      $this->assertIsA($subtasks, 'DBResult', 'Subtasks are loaded from database');
      $this->assertEqual($subtasks->count(), 1, 'One subtask loaded');
      $this->assertIsA($subtasks->getRowAt(0), 'Subtask', 'First element is subtask instance');
      $this->assertTrue($subtasks->getRowAt(0)->getParent()->is($task_copy), 'Task copy is subtask parent');
      
      $this->assertTrue($subtasks->getRowAt(0)->subscriptions()->isSubscribed($this->second_user, false), 'Second user is subscribed to subtask clone');
      
      $this->assertEqual(ApplicationObjects::getContext($subtasks->getRowAt(0)), ApplicationObjects::getContext($task_copy) . '/subtasks/' . $subtasks->getRowAt(0)->getId(), 'Subtask context is OK');
    } // testCopy
    
    function testCopyAndPreserveCategory() {
      $task_copy = $this->active_task->copyToProjectAndPreserveCategory($this->target_project);
      
      $this->assertIsA($task_copy, 'Task', 'Task copied');
      $this->assertEqual($task_copy->getProjectId(), $this->target_project->getId(), 'Copied to target project');
      
      $this->assertIsA($task_copy->category()->get(), 'TaskCategory', 'Copy has a good task category');
      $this->assertEqual($task_copy->category()->get()->getId(), $this->target_project_general_tasks->getId(), 'Task category ID was updated to matching category in target project');
    } // testCopyAndPreserveCategory
    
    function testMoveAndPreserveCategory() {
      $this->active_task->moveToProjectAndPreserveCategory($this->target_project);
      
      $this->assertEqual($this->active_task->getProjectId(), $this->target_project->getId(), 'Moved to target project');
      
      $this->assertIsA($this->active_task->category()->get(), 'TaskCategory', 'Task has a good task category');
      $this->assertEqual($this->active_task->category()->get()->getId(), $this->target_project_general_tasks->getId(), 'Task category ID was updated to matching category in target project');
    } // testMoveAndPreserveCategory
    
    private function attachFileAndAssertAttachmentProperties(IAttachments $parent) {
      $parent->attachments()->attachFile($this->test_file_path, basename($this->test_file_path), 'image/jpeg', $this->logged_user, true);
      $this->assertEqual($parent->attachments()->count($this->logged_user, false), 1, 'One file attached');
      
      $attachments = $parent->attachments()->get($this->logged_user);
      
      $this->assertIsA($attachments, 'DBResult', 'We have attachments');
      $this->assertEqual($attachments->count(), 1, 'One attachment');
      $this->assertIsA($attachments->getRowAt(0), 'Attachment', 'First element is attachment');
      $this->assertEqual($attachments->getRowAt(0)->getParentType(), get_class($parent));
      $this->assertEqual($attachments->getRowAt(0)->getParentId(), $parent->getId());
      $this->assertEqual($attachments->getRowAt(0)->getName(), 'test.jpg');
    } // attachFileAndAssertAttachmentProperties
    
  }