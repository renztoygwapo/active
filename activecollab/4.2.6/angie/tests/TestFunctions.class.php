<?php

  class TestFunctions extends UnitTestCase {
    
    var $support_path;
  
    function testCreateDir() {
      $this->support_path = dirname(__FILE__) . '/support';
      
      $test_folder = $this->support_path . '/test_create_dir';
      
      $this->assertTrue(create_dir($test_folder));
      $this->assertTrue(file_exists($test_folder));
      $this->assertTrue(rmdir($test_folder));
      
      $this->assertTrue(create_dir($test_folder, true));
      $this->assertTrue(file_exists($test_folder));
      $this->assertTrue(is_writable($test_folder));
      $this->assertTrue(rmdir($test_folder));
    } // testCreateDir
  
  }