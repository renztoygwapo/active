<?php

  class TestTaskModificationLog extends AngieModelTestCase {
  
    function testTrackedFields() {
      $task = new Task();
      
      $this->assertIsA($task, 'Task', 'Task instance');
      $this->assertIsA($task->history(), 'IHistoryImplementation', 'Valid history helper instance');
      $this->assertIsA($task->history()->getRenderer(), 'HistoryRenderer', 'Valid renderer instance');
      
      $fields = $task->history()->getTrackedFields();
      
      $this->assertTrue(in_array('project_id', $fields));
      $this->assertTrue(in_array('milestone_id', $fields));
      $this->assertTrue(in_array('name', $fields));
      $this->assertTrue(in_array('body', $fields));
      $this->assertTrue(in_array('integer_field_1', $fields));
      $this->assertTrue(in_array('state', $fields));
      $this->assertTrue(in_array('visibility', $fields));
      $this->assertTrue(in_array('due_on', $fields));
      $this->assertTrue(in_array('assignee_id', $fields));
      $this->assertTrue(in_array('label_id', $fields));
      $this->assertTrue(in_array('category_id', $fields));
      $this->assertTrue(in_array('priority', $fields));
    } // testTrackedFields
    
  }
  