<?php

  /**
   * Logger class
   *
   * @package angie.library.logger
   */
  final class Logger {
    
    /**
     * Log levels
     */
    const INFO = 0;
    const NOTICE = 1;
    const WARNING = 2;
    const ERROR = 3;
  
    /**
     * Logger messages
     * 
     * @var array
     */
    static private $messages = array();
    
    /**
     * Grouped messages
     * 
     * @var array
     */
    static private $grouped_messages = array();
    
    /**
     * Set max number of messages that will be logged
     *
     * @var integer
     */
    static private $max_messages = 500;
    
    /**
     * Add message to log
     *
     * @param string $message
     * @param integer $level
     * @param string $group
     */
    static function log($message, $level = Logger::INFO, $group = null) {
      $log_entry = array($message, $level);
      
      $max_messages_reached = self::$max_messages && count(self::$messages) >= self::$max_messages;
      
      if($max_messages_reached) {
        array_pop(self::$messages);
      } // if
      
      self::$messages[] = $log_entry;
      
      if($group) {
        if(!isset(self::$grouped_messages[$group])) {
          self::$grouped_messages[$group] = array();
        } // if
        
        if($max_messages_reached) {
          array_pop(self::$grouped_messages[$group]);
        } // if
        
        self::$grouped_messages[$group][] = $log_entry;
      } // if
    } // log
    
    /**
     * Return max messages value
     * 
     * @return integer
     */
    static function getMaxMessages() {
      return self::$max_messages;
    } // getMaxMessages
    
    /**
     * Set max messages value
     * 
     * @param integer $max_messages
     */
    static function setMaxMessages($max_messages) {
      self::$max_messages = $max_messages;
    } // setMaxMessages
    
    /**
     * Log entries to file
     *
     * @param string $path
     * @return bool
     */
    static function logToFile($path) {
      $h = fopen($path, 'a');

      if($h) {
        fwrite($h, "Logged on: " . date(DATE_COOKIE) . "\nMemory usage: " . format_file_size(memory_get_usage()) . "\nAvailable groups: " . implode(', ', array_merge(array('all'), array_keys(self::$grouped_messages))) . "\n\nall:\n\n");

        $counter = 1;
        foreach(self::$messages as $entry) {
          list($message, $level) = $entry;
          fwrite($h, self::prepareMessageForFile($message, $level, $counter));
          $counter++;
        } // foreach

        foreach(self::$grouped_messages as $group => $messages) {
          fwrite($h, "\n$group\n\n");

          $counter = 1;
          foreach($messages as $entry) {
            list($message, $level) = $entry;
            fwrite($h, self::prepareMessageForFile($message, $level, $counter));
            $counter++;
          } // foreach
        } // foreach

        fwrite($h, "\n======================================================\n\n");
        fclose($h);

        return true;
      } // if

      return false;
    } // logToFile
    
    /**
     * Format single message to be saved into file
     *
     * @param string $message
     * @param integer $level
     * @param integer $counter
     * @return string
     */
    private static function prepareMessageForFile($message, $level, $counter) {
      $level_string = '<unknown>';
      switch($level) {
        case Logger::INFO:
          $level_string = 'info';
          break;
        case Logger::NOTICE:
          $level_string = 'notice';
          break;
        case Logger::WARNING:
          $level_string = 'warning';
          break;
        case Logger::ERROR:
          $level_string = 'error';
          break;
      } // switch
      
      return "#$counter - $level_string - $message\n";
    } // prepareMessageForFile
  
  }