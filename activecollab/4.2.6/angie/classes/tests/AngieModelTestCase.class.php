<?php

  /**
   * Model test case
   *
   * @package angie.library.tests
   */
  abstract class AngieModelTestCase extends UnitTestCase {
    
    /**
     * Drop and initialize model before each test
     */
    function setUp() {
      AngieApplicationModel::revert('test');
      
      if(AngieApplication::isFrameworkLoaded('search')) {
        Search::initialize();
      } // if
      
      AngieApplication::cache()->clear();
      DataObjectPool::clear();
      
      Authentication::useProvider('AuthenticationProvider');
      Authentication::getProvider()->logUserIn(Users::findById(1));
    } // setUp
    
    /**
     * Assert whether $first string contains $second string
     * 
     * @param string $first
     * @param string $second
     * @param string $message
     */
    function assertContains($first, $second, $message = '%s') {
    	$this->assertTrue(strpos($first, $second) !== false, $message);
    } // assertContains
    
  }