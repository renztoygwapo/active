<?php

  class TestTaskComments extends AngieModelTestCase {
    
    private $logged_user;
    
    private $active_project;
    
    private $active_task;
    
    function setUp() {
      parent::setUp();
      
      $this->logged_user = Users::findById(1);
      
      $this->active_project = new Project();
      $this->active_project->setAttributes(array(
        'name' => 'Application', 
        'leader_id' => 1, 
        'company_id' => 1, 
      ));
      $this->active_project->save();
      
      $this->active_task = new Task();
      $this->active_task->setName('Task 1');
      $this->active_task->setProject($this->active_project);
      $this->active_task->setCreatedBy($this->logged_user);
      $this->active_task->setState(STATE_VISIBLE);
      $this->active_task->setVisibility(VISIBILITY_NORMAL);
      $this->active_task->save();
    } // setUp
    
    function tearDown() {
      $this->logged_user = null;
      $this->active_project = null;
      $this->active_task = null;
    } // tearDown
    
    function testInitialization() {
      $this->assertTrue($this->logged_user->isLoaded(), 'Test user loaded');
      $this->assertTrue($this->active_project->isLoaded(), 'Test project created');
      $this->assertTrue($this->active_task->isLoaded(), 'Test task created');
    } // testInitialization
  
    function testNewComment() {
      $comment = $this->active_task->comments()->submit('Test Comment', $this->logged_user);
      
      $this->assertIsA($comment, 'TaskComment', 'Comment is created');
      $this->assertTrue($this->active_task->is($comment->getParent()));
      $this->assertEqual($this->active_task->comments()->count($this->logged_user, false), 1, 'Comments counter works');
      
      $second_comment = $this->active_task->comments()->submit('Test Comment 2', $this->logged_user);
      
      $this->assertIsA($second_comment, 'TaskComment', 'Second comment is created');
      $this->assertTrue($this->active_task->is($second_comment->getParent()));
      $this->assertEqual($this->active_task->comments()->count($this->logged_user, false), 2, 'Comments counter works');
      
      $comments = $this->active_task->comments()->get($this->logged_user);
      
      $this->assertIsA($comments, 'DBResult', 'We go valid response');
      $this->assertEqual($comments->count(), 2, 'Number of comments that is returned is correct');
    } // testNewComment
    
    function testStateCascade() {
      $comment = $this->active_task->comments()->submit('Test Comment', $this->logged_user);
      
      $this->assertTrue($comment->isLoaded(), 'Comment is created');
      $this->assertEqual($this->active_task->getState(), $comment->getState(), 'Comment has the same state as parent task');
      
      // Move to trash
      $this->active_task->state()->trash();
      
      $this->assertEqual($this->active_task->getState(), STATE_TRASHED, 'Trask is trashed');
      
      $comment = Comments::findById($comment->getId()); // Reload
      $this->assertEqual($this->active_task->getState(), $comment->getState(), 'Comment was trashed as well');
      
      $this->active_task->state()->untrash();
      
      $this->assertEqual($this->active_task->getState(), STATE_VISIBLE, 'Task was restored from trash');
      
      $comment = Comments::findById($comment->getId()); // Reload
      $this->assertEqual($this->active_task->getState(), $comment->getState(), 'Comment was trashed as well');
    } // testStateCascade
    
  }