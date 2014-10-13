<?php

  /**
   * BaseTestEmailObjects class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  abstract class BaseTestEmailObjects extends DataManager {
  
    /**
     * Do a SELECT query over database with specified arguments
     * 
     * This static function can return single instance or array of instances that match 
     * requirements provided in $arguments associative array
     *
     * @param array $arguments Array of query arguments. Fields:
     * 
     *  - one        - select first row
     *  - conditions - additional conditions
     *  - order      - order by string
     *  - offset     - limit offset, valid only if limit is present
     *  - limit      - number of rows that need to be returned
     * 
     * @return mixed
     * @throws DBQueryError
     */
    static function find($arguments = null) {
            
      return parent::find($arguments, TABLE_PREFIX . 'test_email_objects', DataManager::CLASS_NAME_FROM_TABLE, 'TestEmailObject', '');
    } // find
    
    /**
     * Return array of objects that match specific SQL
     *
     * @param string $sql
     * @param array $arguments
     * @param boolean $one
     * @return mixed
     */
    static function findBySQL($sql, $arguments = null, $one = false) {
      return parent::findBySQL($sql, $arguments, $one, TABLE_PREFIX . 'test_email_objects', DataManager::CLASS_NAME_FROM_TABLE, 'TestEmailObject', '');
    } // findBySQL
    
    /**
     * Return object by ID
     *
     * @param mixed $id
     * @return TestEmailObject
     */
    static function findById($id) {
      return parent::findById($id, TABLE_PREFIX . 'test_email_objects', DataManager::CLASS_NAME_FROM_TABLE, 'TestEmailObject', '');
    } // findById
    
    /**
     * Return paginated result
     * 
     * This static function will return paginated result as array. First element of 
     * returned array is array of items that match the request. Second parameter 
     * is Pager class instance that holds pagination data (total pages, current 
     * and next page and so on)
     *
     * @param array $arguments
     * @param integer $page
     * @param integer $per_page
     * @return array
     * @throws DBQueryError
     */
    static function paginate($arguments = null, $page = 1, $per_page = 10) {
      return parent::paginate($arguments, $page, $per_page, TABLE_PREFIX . 'test_email_objects', DataManager::CLASS_NAME_FROM_TABLE, 'TestEmailObject', '');
    } // paginate
    
    /**
     * Return number of rows in this table
     *
     * @param string $conditions Query conditions
     * @return integer
     * @throws DBQueryError
     */
    static function count($conditions = null) {
      return parent::count($conditions, TABLE_PREFIX . 'test_email_objects');
    } // count
    
    /**
     * Update table
     * 
     * $updates is associative array where key is field name and value is new 
     * value
     *
     * @param array $updates
     * @param string $conditions
     * @return boolean
     * @throws DBQueryError
     */
    static function update($updates, $conditions = null) {
      return parent::update($updates, $conditions, TABLE_PREFIX . 'test_email_objects');
    } // update
    
    /**
     * Delete all rows that match given conditions
     *
     * @param string $conditions Query conditions
     * @param string $table_name
     * @return boolean
     * @throws DBQueryError
     */
    static function delete($conditions = null) {
      return parent::delete($conditions, TABLE_PREFIX . 'test_email_objects');
    } // delete
  
  }

?>