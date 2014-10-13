<?php

  /**
   * Angie experiments delegate
   *
   * @package angie.library.application
   * @subpackage delegates
   */
  class AngieExperimentsDelegate extends AngieDelegate {

    /**
     * Array of running experiments
     *
     * @var array
     */
    private $experiments = array();

    /**
     * Construct new experiment delegate instance
     */
    function __construct() {
      if(defined('ANGIE_EXPERIMENTS') && ANGIE_EXPERIMENTS) {
        $this->experiments = explode(',', ANGIE_EXPERIMENTS);
      } // if
    } // __construct

    /**
     * Returns true if $experiment is running
     *
     * @param string $experiment
     * @return bool
     */
    function isRunning($experiment) {
      return in_array($experiment, $this->experiments);
    } // isRunning

  }