<?php

  /**
   * General authentication framework tests
   *
   * @package angie.frameworks.authentication
   * @subpackage tests
   */
  class TestAuthenticationFramework extends AngieModelTestCase {
    
    /**
     * Test if we have parent module loaded
     */
    function testInjectInto() {
      $this->assertTrue(AngieApplication::isModuleLoaded(AUTHENTICATION_FRAMEWORK_INJECT_INTO));
    } // testInjectInto
    
  }