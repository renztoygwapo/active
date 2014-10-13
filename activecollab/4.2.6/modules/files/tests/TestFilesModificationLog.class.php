<?php

  class TestFilesModificationLog extends AngieModelTestCase {
  
    function testFileFields() {
      $file = new File();
      
      $this->assertIsA($file, 'File', 'File instance');
      $this->assertIsA($file->history(), 'IHistoryImplementation', 'Valid history helper instance');
      $this->assertIsA($file->history()->getRenderer(), 'HistoryRenderer', 'Valid renderer instance');
      
      $fields = $file->history()->getTrackedFields();
      
      $this->assertTrue(in_array('project_id', $fields));
      $this->assertTrue(in_array('milestone_id', $fields));
      $this->assertTrue(in_array('category_id', $fields));
      $this->assertTrue(in_array('name', $fields));
      $this->assertTrue(in_array('body', $fields));
      $this->assertTrue(in_array('state', $fields));
      $this->assertTrue(in_array('visibility', $fields));
    } // testFileFields
    
    function testTextDocumentFields() {
      $text_document = new TextDocument();
      
      $this->assertIsA($text_document, 'TextDocument', 'Text document instance');
      $this->assertIsA($text_document->history(), 'IHistoryImplementation', 'Valid history helper instance');
      $this->assertIsA($text_document->history()->getRenderer(), 'HistoryRenderer', 'Valid renderer instance');
      
      $fields = $text_document->history()->getTrackedFields();
      
      $this->assertTrue(in_array('project_id', $fields));
      $this->assertTrue(in_array('milestone_id', $fields));
      $this->assertTrue(in_array('category_id', $fields));
      $this->assertTrue(in_array('name', $fields));
      $this->assertTrue(in_array('body', $fields));
      $this->assertTrue(in_array('state', $fields));
      $this->assertTrue(in_array('visibility', $fields));
    } // testFileFields
    
  }