<?php

  /**
   * MSSQL database result
   *
   * @package angie.library.database
   * @subpackage mssql
   */
  class MSSQLDB2Result extends DBResult {
    
    /**
     * Set cursor to a given position in the record set
     *
     * @param integer $num
     * @return boolean
     */
    public function seek($num) {
      if($num >= 0 && $num <= $this->count() - 1) {
        if(!mssql_data_seek($this->resource, $num)) {
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
     */
    public function next() {
      if($this->cursor_position < $this->count()) { // Not count() - 1 because we use this for getting the current row
        if($row = mssql_fetch_array($this->resource)) {
          $this->setCurrentRow($row);
        } else {
          $query = "select @@ERROR as ErrorCode";
	      $query_result = mssql_query($query,$this->link);
	      $result = mssql_fetch_object($query_result);
	      mssql_free_result($query_result);
          $error_number = $result->ErrorCode;
          
          if($error_number) {
            throw new DBError($error_number, mssql_get_last_message(), 'Failed to seek next row in record set');
          } else {
            return false;
          } // if
        } // if
        
        $this->cursor_position++;
        return true;
      } //if
      
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
        $this->count = mssql_num_rows($this->resource);
      } // if
      return $this->count;
    } // count
    
    /**
     * Free resource when we are done with this result
     *
     * @return boolean
     */
    public function free() {
      return mssql_free_result($this->resource);
    } // free
  }
?>