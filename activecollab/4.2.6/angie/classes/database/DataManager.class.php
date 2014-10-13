<?php
  
  /**
   * Data manager class
   *
   * This class provides interface for extracting multiple rows form a specific 
   * table and population of item objects with extracted data
   * 
   * @package angie.library.database
   */
  abstract class DataManager {

    // ---------------------------------------------------
    //  Model signature methods
    // ---------------------------------------------------

    /**
     * Return name of this model
     *
     * @param boolean $underscore
     * @return string
     * @throws NotImplementedError
     */
    static function getModelName($underscore = false) {
      throw new NotImplementedError(__METHOD__);
    } // getModelName

    /**
     * Return name of the table where system will persist model instances
     *
     * @param boolean $with_prefix
     * @return string
     * @throws NotImplementedError
     */
    static function getTableName($with_prefix = true) {
      throw new NotImplementedError(__METHOD__);
    } // getTableName

    /**
     * Return class name of a single instance
     *
     * @return string
     * @throws NotImplementedError
     */
    static function getInstanceClassName() {
      throw new NotImplementedError(__METHOD__);
    } // getInstanceClassName

    /**
     * Return whether instance class name should be loaded from a field, or based on table name
     *
     * @return string
     * @throws NotImplementedError
     */
    static function getInstanceClassNameFrom() {
      throw new NotImplementedError(__METHOD__);
    } // getInstanceClassNameFrom

    /**
     * Return name of the field from which we will read instance class
     *
     * @return string
     * @throws NotImplementedError
     */
    static function getInstanceClassNameFromField() {
      throw new NotImplementedError(__METHOD__);
    } // getInstanceClassNameFromField

    /**
     * Return name of this model
     *
     * @return string
     * @throws NotImplementedError
     */
    static function getDefaultOrderBy() {
      throw new NotImplementedError(__METHOD__);
    } // getDefaultOrderBy

    // ---------------------------------------------------
    //  Magic
    // ---------------------------------------------------

    /**
     * Create new instance
     *
     * In case of regular model, $p1 is attributes array and $p2 is save flag (save or not)
     * In case of polymorh model, $p1 is class name, $p2 is attributes array and $p3 is save flag (save or not)
     *
     * @param mixed $p1
     * @param mixed $p2
     * @param mixed $p3
     * @return DataObject
     * @throws InvalidParamError
     */
    static function create($p1, $p2 = null, $p3 = null) {
      $class_name_from = static::getInstanceClassNameFrom();

      if($class_name_from == self::CLASS_NAME_FROM_FIELD) {
        $class_name = $p1;

        if(is_string($class_name) && class_exists($class_name)) {
          $instance_class_name = static::getInstanceClassName();

          if(!is_subclass_of($class_name, $instance_class_name)) {
            throw new InvalidParamError('p1', $p1, "Class '$class_name' does not extend '$instance_class_name'");
          } // if
        } else {
          throw new InvalidParamError('p1', $p1, 'First parameter is expected to be a class name (polymorh model)');
        } // if

        $attributes = $p2;
        $save = (boolean) $p3;
      } else {
        $class_name = static::getInstanceClassName();
        $attributes = $p1;
        $save = (boolean) $p2;
      } // if

      $instance = new $class_name;
      if($attributes && is_foreachable($attributes)) {
        $instance->setAttributes($attributes);
      } // if

      if($save) {
        $instance->save();
      } // if

      return $instance;
    } // create
    
    /**
     * How do we know which class name to use
     * 
     * - CLASS_NAME_FROM_TABLE - Class name from table name, value is prepared 
     *   by generator
     * - CLASS_NAME_FROM_FIELD - Load class name from row field
     */
    const CLASS_NAME_FROM_TABLE = 0;
    const CLASS_NAME_FROM_FIELD = 1;
    
    /**
     * Do a SELECT query over database with specified arguments
     * 
     * This function can return single instance or array of instances that match 
     * requirements provided in $arguments associative array
     * 
     * $arguments is an associative array with following fields (all optional):
     * 
     *  - one        - select first row
     *  - conditions - additional conditions
     *  - group      - group by string
     *  - having     - having string
     *  - order      - order by string
     *  - offset     - limit offset, valid only if limit is present
     *  - limit      - number of rows that need to be returned
     *
     * @param array $arguments
     * @param string $table_name
     * @param integer $class_name_from
     * @param string $class_name_from_value
     * @return DBResult
     * @throws DBQueryError
     */
    static function find($arguments = null, $table_name = null, $class_name_from = DataManager::CLASS_NAME_FROM_TABLE, $class_name_from_value = null) {
      if($arguments && isset($arguments['one']) && $arguments['one']) {
        return static::findOneBySQL(static::prepareSelectFromArguments($arguments));
      } else {
        return static::findBySQL(static::prepareSelectFromArguments($arguments));
      } // if
    } // find

    /**
     * Return multiple records by their ID-s
     *
     * @param array $ids
     * @return DBResult
     */
    static function findByIds($ids) {
      return static::find(array(
        'conditions' => array('id IN (?)', $ids),
      ));
    } // findByIds
    
    /**
     * Return object of a specific class by SQL
     *
     * @return DBResult
     * @throws InvalidParamError
     */
    static function findBySQL() {
      $arguments = func_get_args();

      if(empty($arguments)) {
        throw new InvalidParamError('arguments', $arguments, 'DataManager::findOneBySql() function requires at least SQL query to be provided');
      } // if

      $sql = array_shift($arguments);

      if($arguments !== null) {
        $sql = DB::getConnection()->prepare($sql, $arguments);
      } // if

      $class_name_from = static::getInstanceClassNameFrom();

      switch($class_name_from) {
        case self::CLASS_NAME_FROM_FIELD:
          return DB::getConnection()->execute($sql, null, DB::LOAD_ALL_ROWS, DB::RETURN_OBJECT_BY_FIELD, static::getInstanceClassNameFromField());
        case self::CLASS_NAME_FROM_TABLE:
          return DB::getConnection()->execute($sql, null, DB::LOAD_ALL_ROWS, DB::RETURN_OBJECT_BY_CLASS, static::getInstanceClassName());
        default:
          throw new InvalidParamError('class_name_from', $class_name_from, 'Unexpected value');
      } // switch
    } // findBySQL

    /**
     * Find a single instance by SQL
     *
     * @return DBResult
     * @throws Error
     * @throws InvalidParamError
     */
    static function findOneBySql() {
      $arguments = func_get_args();

      if(empty($arguments)) {
        throw new InvalidParamError('arguments', $arguments, 'DataManager::findOneBySql() function requires at least SQL query to be provided');
      } // if

      $sql = array_shift($arguments);

      if(count($arguments)) {
        $sql = DB::getConnection()->prepare($sql, $arguments);
      } // if

      if($row = DB::executeFirstRow($sql)) {
        switch(static::getInstanceClassNameFrom()) {
          case self::CLASS_NAME_FROM_FIELD:
            $class_name = $row[static::getInstanceClassNameFromField()];
            break;
          case self::CLASS_NAME_FROM_TABLE:
            $class_name = static::getInstanceClassName();
            break;
          default:
            throw new Error('Unknown load instance class name from method: ' . static::getInstanceClassNameFrom());
        } // switch

        $item = new $class_name();
        $item->loadFromRow($row, true);
        return $item;
      } else {
        return null;
      } // if
    } // findOneBySql
    
    /**
     * Return paginated result
     * 
     * This function will return paginated result as array. First element of 
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
      if(empty($arguments)) {
        $arguments = array();
      } // if
      
      $arguments['limit'] = $per_page;
      $arguments['offset'] = ($page - 1) * $per_page;
      
      return array(
        static::find($arguments),
        new Pager(static::count(array_var($arguments, 'conditions'), static::getTableName()), $page, $per_page)
      ); // array
    } // paginate
    
    /**
     * Return object by ID
     *
     * @param mixed $id
     * @return DataObject
     * @throws InvalidParamError
     */
    static function findById($id) {
      if(empty($id)) {
        return null;
      } elseif(is_numeric($id)) {

      } else {
        throw new InvalidParamError('id', $id, '$id can only be a number');
      } // if

      $table_name = static::getTableName();

      $cached_row = AngieApplication::cache()->get(static::getCacheKeyForObject($id), function() use ($table_name, $id) {
        return DB::executeFirstRow("SELECT * FROM $table_name WHERE id = ? LIMIT 0, 1", $id);
      });

      if($cached_row) {
        $class_name_from = static::getInstanceClassNameFrom();

        switch($class_name_from) {
          case self::CLASS_NAME_FROM_FIELD:
            $class_name = $cached_row[static::getInstanceClassNameFromField()];
            break;
          case self::CLASS_NAME_FROM_TABLE:
            $class_name = static::getInstanceClassName();
            break;
          default:
            throw new InvalidParamError('class_name_from', $class_name_from, 'Unexpected value');
        } // switch

        $item = new $class_name();
        $item->loadFromRow($cached_row);

        return $item;
      } else {
        return null;
      } // if
    } // findById

    /**
     * Get cache key for a given object
     *
     * @param DataObject|integer $object_or_object_id
     * @param mixed $subnamespace
     * @return array
     * @throws InvalidParamError
     */
    static function getCacheKeyForObject($object_or_object_id, $subnamespace = null) {
      $instance_class = static::getInstanceClassName();

      if($object_or_object_id instanceof $instance_class) {
        return get_data_object_cache_key(static::getModelName(true), $object_or_object_id->getId(), $subnamespace);
      } elseif(is_numeric($object_or_object_id)) {
        return get_data_object_cache_key(static::getModelName(true), $object_or_object_id, $subnamespace);
      } else {
        throw new InvalidParamError('object_or_object_id', $object_or_object_id, "object_or_object_id needs to either instance of $instance_class or ID");
      } // if
    } // getCacheKeyForObject
    
    /**
     * Return number of rows in this table
     *
     * @param string $conditions Query conditions
     * @return integer
     * @throws DBQueryError
     */
    static function count($conditions = null) {
      $table_name = static::getTableName();

      $conditions = trim(DB::prepareConditions($conditions));

      if($conditions) {
        return (integer) DB::executeFirstCell("SELECT COUNT(*) AS 'row_count' FROM $table_name WHERE $conditions");
      } else {
        return (integer) DB::executeFirstCell("SELECT COUNT(*) AS 'row_count' FROM $table_name");
      } // if
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
      $table_name = static::getTableName();

      $updates_part = array();
      foreach($updates as $field => $value) {
        $updates_part[] = $field . ' = ' . DB::escape($value);
      } // foreach
      $updates_part = implode(',' , $updates_part);
      
      $conditions = DB::prepareConditions($conditions);
      
      $where_string = trim($conditions) == '' ? '' : "WHERE $conditions";
      return DB::execute("UPDATE $table_name SET $updates_part $where_string");
    } // update
    
    /**
     * Delete all rows that match given conditions
     *
     * @param string $conditions Query conditions
     * @return boolean
     */
    static function delete($conditions = null) {
      $table_name = static::getTableName();

      if($conditions = trim(DB::prepareConditions($conditions))) {
        return DB::execute("DELETE FROM $table_name WHERE $conditions");
      } else {
        return DB::execute("DELETE FROM $table_name");
      } // if
    } // delete
    
    /**
     * Prepare SELECT query string from arguments and table name
     *
     * @param array $arguments
     * @return string
     */
    static function prepareSelectFromArguments($arguments = null) {
      $one = (boolean) (isset($arguments['one']) && $arguments['one']);
      $conditions = isset($arguments['conditions']) ? DB::prepareConditions($arguments['conditions']) : '';
      $group_by = isset($arguments['group']) ? $arguments['group'] : '';
      $having = isset($arguments['having']) ? $arguments['having'] : '';
      $order_by = isset($arguments['order']) ? $arguments['order'] : static::getDefaultOrderBy();
      $offset = isset($arguments['offset']) ? (integer) $arguments['offset'] : 0;
      $limit = isset($arguments['limit']) ? (integer) $arguments['limit'] : 0;
      
      if($one && $offset == 0 && $limit == 0) {
        $limit = 1; // Narrow the query
      } // if

      $table_name = static::getTableName();
      $where_string = trim($conditions) == '' ? '' : "WHERE $conditions";
      $group_by_string = trim($group_by) == '' ? '' : "GROUP BY $group_by";
      $having_string = trim($having) == '' ? '' : "HAVING $having";
      $order_by_string = trim($order_by) == '' ? '' : "ORDER BY $order_by";
      $limit_string = $limit > 0 ? "LIMIT $offset, $limit" : '';
      
      return "SELECT * FROM $table_name $where_string $group_by_string $having_string $order_by_string $limit_string";
    } // prepareSelectFromArguments
    
  }