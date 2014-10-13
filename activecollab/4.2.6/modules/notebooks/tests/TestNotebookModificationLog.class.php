<?php

  class TestNotebookModificationLog extends AngieModelTestCase {
    
    function testNotebookFields() {
      $notebook = new Notebook();
      
      $this->assertIsA($notebook, 'Notebook', 'Notebook page instance');
      $this->assertIsA($notebook->history(), 'IHistoryImplementation', 'Valid history helper instance');
      $this->assertIsA($notebook->history()->getRenderer(), 'HistoryRenderer', 'Valid renderer instance');
      
      $fields = $notebook->history()->getTrackedFields();
      
      $this->assertTrue(in_array('name', $fields));
      $this->assertTrue(in_array('body', $fields));
      $this->assertTrue(in_array('state', $fields));
      $this->assertTrue(in_array('visibility', $fields));
      $this->assertTrue(in_array('project_id', $fields));
      $this->assertTrue(in_array('milestone_id', $fields));
    } // testNotebookFields
  
    function testNotebookPageFields() {
      $notebook_page = new NotebookPage();
      
      $this->assertIsA($notebook_page, 'NotebookPage', 'Notebook page instance');
      $this->assertIsA($notebook_page->history(), 'IHistoryImplementation', 'Valid history helper instance');
      $this->assertIsA($notebook_page->history()->getRenderer(), 'HistoryRenderer', 'Valid renderer instance');
      
      $fields = $notebook_page->history()->getTrackedFields();
      
      $this->assertTrue(in_array('parent_type', $fields));
      $this->assertTrue(in_array('parent_id', $fields));
      $this->assertTrue(in_array('name', $fields));
      $this->assertTrue(in_array('body', $fields));
      $this->assertTrue(in_array('state', $fields));
      $this->assertTrue(!in_array('visibility', $fields), 'Notebook pages dont have visibility');
      $this->assertTrue(in_array('is_locked', $fields));
    } // testNotebookPageFields
    
  }