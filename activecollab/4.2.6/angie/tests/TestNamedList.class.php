<?php

  class TestNamedList extends UnitTestCase {
    
    function testAdd() {
      $list = new NamedList();
      
      $list->add('first', 'value1');
      $list->add('second', 'value2');
      
      $this->assertTrue(count($list) > 0);
      
      $counter = 0;
      foreach($list as $k => $v) {
        $counter++;
        
        if($counter == 1) {
          $this->assertEqual($k, 'first');
          $this->assertEqual($v, 'value1');
        } elseif($counter == 2) {
          $this->assertEqual($k, 'second');
          $this->assertEqual($v, 'value2');
        }
      }
    } // testAdd
    
    function testBeginWith() {
      $list = new NamedList();
      
      $list->add('second', 'value2');
      $list->add('third', 'value3');
      
      $list->beginWith('first', 'value1');
      
      $this->assertTrue(count($list) > 0);
      
      $counter = 0;
      foreach($list as $k => $v) {
        $counter++;
        
        switch($counter) {
          case 1:
            $this->assertEqual($k, 'first');
            $this->assertEqual($v, 'value1');
            break;
          case 2:
            $this->assertEqual($k, 'second');
            $this->assertEqual($v, 'value2');
            break;
          case 3:
            $this->assertEqual($k, 'third');
            $this->assertEqual($v, 'value3');
            break;
        }
      }
    } // testBeginWith
    
    function testAddBefore() {
      $list = new NamedList();
      
      $list->add('first', 'value1');
      $list->add('third', 'value3');
      
      $list->addBefore('second', 'value2', 'third');
      $list->addBefore('fourth', 'value4', 'does_not_exist');
      
      $this->assertTrue(count($list) > 0);
      
      $counter = 0;
      foreach($list as $k => $v) {
        $counter++;
        
        switch($counter) {
          case 1:
            $this->assertEqual($k, 'first');
            $this->assertEqual($v, 'value1');
            break;
          case 2:
            $this->assertEqual($k, 'second');
            $this->assertEqual($v, 'value2');
            break;
          case 3:
            $this->assertEqual($k, 'third');
            $this->assertEqual($v, 'value3');
            break;
          case 4:
            $this->assertEqual($k, 'fourth');
            $this->assertEqual($v, 'value4');
            break;
        }
      }
    } // testAddBefore
    
    function testAddAfter() {
      $list = new NamedList();
      
      $list->add('first', 'value1');
      $list->add('third', 'value3');
      
      $list->addAfter('second', 'value2', 'first');
      $list->addAfter('fourth', 'value4', 'does_not_exist');
      
      $this->assertTrue(count($list) > 0);
      
      $counter = 0;
      foreach($list as $k => $v) {
        $counter++;
        
        switch($counter) {
          case 1:
            $this->assertEqual($k, 'first');
            $this->assertEqual($v, 'value1');
            break;
          case 2:
            $this->assertEqual($k, 'second');
            $this->assertEqual($v, 'value2');
            break;
          case 3:
            $this->assertEqual($k, 'third');
            $this->assertEqual($v, 'value3');
            break;
          case 4:
            $this->assertEqual($k, 'fourth');
            $this->assertEqual($v, 'value4');
            break;
        }
      }
    } // testAddAfter
    
  }

?>