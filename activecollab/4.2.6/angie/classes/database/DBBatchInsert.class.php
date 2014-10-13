<?php

  /**
   * Utility class that can insert multiple rows using a smaller number of 
   * queries by combining them
   * 
   * @package angie.framework.database
   */
  class DBBatchInsert {

    // Insert mode
    const INSERT_RECORDS = 'insert';
    const REPLACE_RECORDS = 'replace';
    
    /**
     * Cached SQL query foundation
     *
     * @var string
     */
    private $sql_foundation;
    
    /**
     * String that's used to prepare statements
     *
     * @var string
     */
    private $row_prepare_pattern;
    
    /**
     * Number of fields that are being inserted
     *
     * @var integer
     */
    private $fields_num;
    
    /**
     * Numbe of rows that are inserted per single INSERT query
     *
     * @var integer
     */
    private $rows_per_batch;
    
    /**
     * Array of rows that will need to be inserted into the database
     *
     * @var array
     */
    private $rows = array();
    
    /**
     * Construct new batch insert instance
     * 
     * @param string $table_name
     * @param array $fields
     * @param integer $rows_per_batch
     * @param string $mode
     * @throws InvalidParamError
     */
    function __construct($table_name, $fields, $rows_per_batch = 50, $mode = DBBatchInsert::INSERT_RECORDS) {
      if(is_array($fields)) {
        $this->fields_num = count($fields);
        
        if($this->fields_num) {
          $question_marks = array();
          
          foreach($fields as $k => $v) {
            $fields[$k] = DB::escapeFieldName($v);
            $question_marks[] = '?';
          } // foreach
        } else {
          throw new InvalidParamError('fields', $fields, 'Fields needs to be an array with at least one element');
        } // if
      } else {
        throw new InvalidParamError('fields', $fields, 'Fields needs to be an array');
      } // if

      if($mode == DBBatchInsert::REPLACE_RECORDS) {
        $this->sql_foundation = 'REPLACE INTO ' . DB::escapeTableName($table_name) . ' (' . implode(', ', $fields) . ') VALUES ';
      } else {
        $this->sql_foundation = 'INSERT INTO ' . DB::escapeTableName($table_name) . ' (' . implode(', ', $fields) . ') VALUES ';
      } // if
      $this->row_prepare_pattern = '(' . implode(', ', $question_marks). ')';
      $this->rows_per_batch = (integer) $rows_per_batch;
      
      if($this->rows_per_batch < 1) {
        $this->rows_per_batch = 50;
      } // if
    } // __construct
    
    /**
     * Insert a row
     */
    function insert() {
      $this->insertArray(func_get_args());
    } // insert
    
    /**
     * Insert array where all elements are values, instead of arguments
     * 
     * @param array $array
     * @throws InvalidParamError
     */
    function insertArray($array) {
      if(is_array($array) && count($array) == $this->fields_num) {
        $this->rows[] = DB::getConnection()->prepare($this->row_prepare_pattern, $array);
        
        if(count($this->rows) == $this->rows_per_batch) {
          $this->insertRows();
        } // if
      } else {
        throw new InvalidParamError('array', $array, "We expect $this->fields_num argument(s)");
      } // if
    } // insertArray

    /**
     * Insert array of already escaped values
     *
     * @param $array
     * @throws InvalidParamError
     */
    function insertEscapedArray($array) {
      if(is_array($array) && count($array) == $this->fields_num) {
        $this->rows[] = '(' . implode(', ', $array) . ')';

        $this->checkAndInsert();
      } else {
        throw new InvalidParamError('array', $array, "We expect $this->fields_num argument(s)");
      } // if
    } // insertEscapedArray
    
    /**
     * Finish with the batch
     */
    function done() {
      $this->insertRows();
    } // done

    /**
     * Check whether we should insert rows and insert them if we do
     */
    private function checkAndInsert() {
      if(count($this->rows) == $this->rows_per_batch) {
        $this->insertRows();
      } // if
    } // checkAndInsert
    
    /**
     * Do insert rows that are already loaded
     */
    private function insertRows() {
      if(count($this->rows) > 0) {
        DB::execute($this->sql_foundation . implode(', ', $this->rows));
        $this->rows = array();
      } // if
    } // insertRows
    
  }