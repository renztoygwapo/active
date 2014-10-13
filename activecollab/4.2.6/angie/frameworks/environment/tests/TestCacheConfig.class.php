<?php

  /**
   * Test caching configuration
   *
   * @package angie.frameworks.environment
   * @subpackage tests
   */
  class TestCacheConfig extends UnitTestCase {

    /**
     * Test memcached server list option parser
     */
    function testMemcachedServerConfig() {
      $this->assertEqual(AngieApplication::cache()->parseMemcachedServersList('127.0.0.1'), array(
        array('127.0.0.1', '11211', 1),
      ));

      $this->assertEqual(AngieApplication::cache()->parseMemcachedServersList('127.0.0.1:123'), array(
        array('127.0.0.1', '123', 1),
      ));

      $this->assertEqual(AngieApplication::cache()->parseMemcachedServersList('127.0.0.1/20'), array(
        array('127.0.0.1', '11211', 20),
      ));

      $this->assertEqual(AngieApplication::cache()->parseMemcachedServersList('127.0.0.1:123/20'), array(
        array('127.0.0.1', '123', 20),
      ));
    } // testMemcachedServerConfig

  }