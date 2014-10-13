<?php

  /**
   * MSSQL database result
   *
   * @package angie.library.database
   * @subpackage mssql
   */
  class MSSQLDBResult extends DBResult {
    
    /**
     * Set cursor to a given position in the record set
     *
     * @param integer $num
     * @return boolean
     */
    public function seek($num) {
      if($num >= 0 && $num <= $this->count() - 1) {
      	if ($num == 0) {
      		if(!sqlsrv_fetch($this->resource,SQLSRV_SCROLL_FIRST)) {
              return false;
      		}
        } else {
	        if(!sqlsrv_fetch($this->resource,SQLSRV_SCROLL_ABSOLUTE,$num - 1)) {
	          return false;
	        } // if
      	} //if
        var_dump($num);
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
      	$old_errors = sqlsrv_errors($this->resource);
        if($row = sqlsrv_fetch_array($this->resource,SQLSRV_FETCH_ASSOC)) {
          $this->setCurrentRow($row);
        } else {
          $new_errors = sqlsrv_errors($this->resource);
          if(count($new_errors) > count($old_errors)) {
          	$last_error = $new_errors[count($new_errors) - 1];
            throw new DBError($last_error['code'], $last_error['message'], 'Failed to seek next row in record set');
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
        $this->count = sqlsrv_num_rows($this->resource);
      } // if
      return $this->count;
    } // count
    
    /**
     * Free resource when we are done with this result
     *
     * @return boolean
     */
    public function free() {
      return sqlsrv_free_stmt($this->resource);
    } // free
  }
?>