<?php

  /**
   * Angie specific test reporter
   *
   * @package angie.library.tests
   */
  class AngieTestReporter extends TextReporter {

    protected $print_backtrace = false;

    /**
     * Print backtrace for exceptions
     *
     * @param bool $print_backtrace
     */
    function __construct($print_backtrace = false) {
      $this->print_backtrace = $print_backtrace;
    } // __construct

    /**
     * Pain exception
     *
     * @param Exception $exception
     */
    function paintException($exception) {
      parent::paintException($exception);

      if($this->print_backtrace) {
        print "\nBacktrace:\n" . $exception->getTraceAsString();
      } // if

      print "\n";
    } // paintException

  }