<?php

  class TestDiscussionModificationLog extends AngieModelTestCase {
    
    function testTrackedFields() {
      $discussion = new Discussion();
      
      $this->assertIsA($discussion, 'Discussion', 'Discussioninstance');
      $this->assertIsA($discussion->history(), 'IHistoryImplementation', 'Valid history helper instance');
      $this->assertIsA($discussion->history()->getRenderer(), 'HistoryRenderer', 'Valid renderer instance');
      
      $fields = $discussion->history()->getTrackedFields();
      
      $this->assertTrue(in_array('project_id', $fields));
      $this->assertTrue(in_array('milestone_id', $fields));
      $this->assertTrue(in_array('name', $fields));
      $this->assertTrue(in_array('body', $fields));
      $this->assertTrue(in_array('boolean_field_1', $fields));
      $this->assertTrue(in_array('state', $fields));
      $this->assertTrue(in_array('visibility', $fields));
      $this->assertTrue(in_array('category_id', $fields));
    } // testTrackedFields
  
  }