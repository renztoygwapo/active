<?php

  /**
   * Class that lets PHP natively iterate over DB results
   *
   * @package angie.library.database
   */
  class DBResultIterator implements Iterator {
    
    /**
     * Result set that is iterated
     *
     * @var DBResult
     */
    private $result;    
    
    /**
     * Construct the iterator
     * 
     * @param DBResult $rs
     */
    public function __construct(DBResult $result) {
      $this->result = $result;
    } // __construct
    
    /**
     * If not at start of resultset, this method will call seek(0).
     * @see ResultSet::seek()
     */
    function rewind() {
      if($this->result->getCursorPosition() > 0) {
        $this->result->seek(0);
      } // if
    } // rewind
    
    /**
     * This method checks to see whether there are more results
     * by advancing the cursor position
     * 
     * @return boolean
     */
    function valid() {
      return $this->result->next();
    } // valid
    
    /**
     * Returns the cursor position
     * 
     * @return int
     */
    function key() {
      return $this->result->getCursorPosition();
    } // key
    
    /**
     * Returns the row (assoc array) at current cursor position
     * 
     * @return array
     */
    function current() {
       return $this->result->getCurrentRow();
    } // current
    
    /**
     * This method does not actually do anything since we have already advanced
     * the cursor pos in valid()
     * 
     * @return null
     */
    function next() {
      
    } // next
    
  }