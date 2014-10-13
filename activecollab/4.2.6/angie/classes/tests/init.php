<?php

  /**
   * Test library initialization file
   * 
   * @package angie.library.tests
   */
  
  require_once ANGIE_PATH . '/vendor/simpletest/unit_tester.php';
  require_once ANGIE_PATH . '/vendor/simpletest/reporter.php';
  require_once ANGIE_PATH . '/vendor/simpletest/mock_objects.php';
  
  require_once ANGIE_PATH . '/classes/tests/AngieTestReporter.class.php';
  require_once ANGIE_PATH . '/classes/tests/AngieModelTestCase.class.php';