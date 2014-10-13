<?php

  class TestDocumentModificationLog extends AngieModelTestCase {

    function testTrackedFields() {
      $document = new Document();
      
      $this->assertIsA($document, 'Document', 'Document instance');
      $this->assertIsA($document->history(), 'IHistoryImplementation', 'Valid history helper instance');
      $this->assertIsA($document->history()->getRenderer(), 'HistoryRenderer', 'Valid renderer instance');
      
      $fields = $document->history()->getTrackedFields();
      
      $this->assertTrue(in_array('category_id', $fields));
      $this->assertTrue(in_array('name', $fields));
      $this->assertTrue(in_array('body', $fields));
      $this->assertTrue(in_array('state', $fields));
      $this->assertTrue(in_array('visibility', $fields));
      $this->assertTrue(in_array('is_pinned', $fields));
    } // testTrackedFields
    
  }