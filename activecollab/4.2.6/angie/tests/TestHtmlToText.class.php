<?php

  /**
   * Test HTML to text conversion
   *
   * @package angie
   * @subpackage tests
   */
  class TestHtmlToText extends UnitTestCase {

    function testConversion() {
      $this->assertEqual(HTML::toPlainText('<h1>test h1!</h1>'), 'TEST H1!', 'Expected to get test');
      $this->assertEqual(HTML::toPlainText('<H2>test h2!</H2>'), 'TEST H2!', 'Expected to get test');
      $this->assertEqual(HTML::toPlainText('<b>test!</b>'), 'TEST!', 'Expected to get test');
      $this->assertEqual(HTML::toPlainText('<strong>test!</strong>'), 'TEST!', 'Expected to get test');
      $this->assertEqual(HTML::toPlainText('<a href="http://www.google.com">Google</a>'), 'Google [http://www.google.com]', 'Expected to get Google');
    } // testConversion

  }