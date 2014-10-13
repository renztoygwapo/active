<?php

  /**
   * Test cache delegate
   *
   * @package angie.tests
   */
  class TestCacheDelegate extends UnitTestCase {

    /**
     * Tear down
     */
    function tearDown() {
      AngieApplication::cache()->clear();
    } // tearDown

    /**
     * Test default values
     */
    function testDefaultValues() {
      $this->assertEqual(AngieApplication::cache()->get('something_to_get', 'default-value'), 'default-value');
      $this->assertEqual(AngieApplication::cache()->get('something_to_get'), 'default-value');
    } // testDefaultValues

    /**
     * Test callback as default value
     */
    function testCallback() {
      $value_to_set = 'value-to-set';

      $this->assertEqual(AngieApplication::cache()->get('something_to_get', function() use ($value_to_set) {
        return $value_to_set;
      }), $value_to_set);
    } // testCallback

  }