<?php

  /**
   * Anige's logger interface for SwiftMailer
   * 
   * @package angie.vendor.swiftmailer
   */
  class SwiftMailerForLogger implements Swift_Plugins_Logger {
  
    /**
     * Add a log entry
     * 
     * @param string $entry
     */
    public function add($entry) {
      Logger::log($entry, Logger::INFO, 'mailing');
    } // add
    
    /**
     * Clear the log contents
     */
    public function clear() {
      
    } // clear
    
    /**
     * Get this log as a string
     * 
     * @return string
     */
    public function dump() {
    
    } // dump
    
  }