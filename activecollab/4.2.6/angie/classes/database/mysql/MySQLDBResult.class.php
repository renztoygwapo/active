<?php

  /**
   * MySQL database result
   *
   * @package angie.library.database
   * @subpackage mysql
   */
  class MySQLDBResult extends DBResult {
    
    /**
     * Set cursor to a given position in the record set
     *
     * @param integer $num
     * @return boolean
     */
    public function seek($num) {
      if($num >= 0 && $num <= $this->count() - 1) {
        if(!mysql_data_seek($this->resource, $num)) {
          return false;
        } // if
        
        $this->cursor_position = $num;
        return true;
      } // if
      
      return false;
    } // seek
    
    /**
     * Return next record in result set
     *
     * @return array
     * @throws DBError
     */
    public function next() {
      if($this->cursor_position < $this->count()) { // Not count() - 1 because we use this for getting the current row
        if($row = mysql_fetch_assoc($this->resource)) {
          $this->setCurrentRow($row);
        } else {
          $error_num = mysql_error($this->resource);
          
          if($error_num) {
            throw new DBError($error_num, mysql_error($this->resource), 'Failed to seek next row in record set');
          } else {
            return false;
          } // if
        } // if
        
        $this->cursor_position++;
        return true;
      } // if
      
      return false;
    } // next
    
    /**
     * Number of rows in recordset
     *
     * @var integer
     */
    private $count = false;
    
    /**
     * Return number of records in result set
     *
     * @return integer
     */
    public function count() {
      if($this->count === false) {
        $this->count = mysql_num_rows($this->resource);
      } // if
      return $this->count;
    } // count
    
    /**
     * Free resource when we are done with this result
     *
     * @return boolean
     */
    public function free() {
      if(is_resource($this->resource)) {
        return mysql_free_result($this->resource);
      } else {
        return true;
      } // if
    } // free
    
  }