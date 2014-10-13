<?php

  class TestSmartyAutoCleanTestObject {
  
    function giveMeSomethingToClean() {
      return 'Ilija Studen <ilija.studen@gmail.com>';
    } // giveMeSomethingToClean
    
  }

  class TestSmartyAutoClean extends UnitTestCase {
  
    function testAutoClean() {
      $smarty = new Smarty();
      
      $smarty->assign(array(
        'test_variable' => 'activeCollab Support <support@activecollab.com>', 
        'test_object' => new TestSmartyAutoCleanTestObject(), 
      ));
      
      $smarty->addDefaultModifiers('clean');
      
      $this->assertEqual($smarty->fetch('string:{$test_variable}'), 'activeCollab Support &lt;support@activecollab.com&gt;', 'Auto-clean works for variables');
      $this->assertEqual($smarty->fetch('string:{$test_variable nofilter}'), 'activeCollab Support <support@activecollab.com>', 'Auto-clean disabled');
      $this->assertEqual($smarty->fetch('string:{$test_object->giveMeSomethingToClean()}'), 'Ilija Studen &lt;ilija.studen@gmail.com&gt;', 'Auto-clean works for object method');
      $this->assertEqual($smarty->fetch('string:{$test_object->giveMeSomethingToClean() nofilter}'), 'Ilija Studen <ilija.studen@gmail.com>', 'Auto-clean disabled');
    } // testAutoClean
    
  }