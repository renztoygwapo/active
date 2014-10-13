<?php

  /**
   * Benchmark timer for angie
   * 
   * @package angie.vendor.benchmark
   */
  final class BenchmarkForAngie {
    
    /**
     * Timer instance
     *
     * @var BenchmarkTimer
     */
    static private $instance;
    
    // ---------------------------------------------------
    //  Start / Stop control
    // ---------------------------------------------------
  
    /**
     * Start the timer
     */
    static function start() {
      if(empty(self::$instance)) {
        self::$instance = new BenchmarkTimer();
      } // if
      
      self::$instance->start();
    } // start
    
    /**
     * Stop the timer
     */
    static function stop() {
      if(self::$instance instanceof BenchmarkTimer) {
        self::$instance->stop();
      } // if
    } // stop
    
    // ---------------------------------------------------
    //  Get information
    // ---------------------------------------------------
    
    /**
     * Return elapsed time
     * 
     * @return float
     */
    static function getTimeElapsed() {
      return self::$instance->TimeElapsed();
    } // getTimeElapsed
    
    /**
     * Return memory usage
     * 
     * @return integer
     */
    static function getMemoryUsage() {
      return memory_get_peak_usage();
    } // getMemoryUsage
    
    /**
     * Return number of executed queries
     * 
     * @return integer
     */
    static function getQueriesCount() {
      return DB::getQueryCount();
    } // getQueriesCount

    /**
     * Return all quries logged by DB layer
     *
     * @return array
     */
    static function getQueries() {
      return AngieApplication::isInDevelopment() ? DB::getAllQueries() : null;
    } // getQueries
    
  }