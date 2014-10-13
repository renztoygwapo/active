<?php

  /**
   * Return data object cache key based on given parameters
   *
   * @param string $model_name
   * @param int $id
   * @param mixed $subnamespace
   * @return array
   */
  function get_data_object_cache_key($model_name, $id, $subnamespace) {
    $key = array('models', $model_name, $id);

    if($subnamespace) {
      if(is_array($subnamespace)) {
        $key = array_merge($key, $subnamespace);
      } else {
        $key[] = $subnamespace;
      } // if
    } // if

    return $key;
  } // get_data_object_cache_key

  /**
   * Data object class
   *
   * This class enables easy implementation of any object that is based
   * on single database row. It enables reading, updating, inserting and 
   * deleting that row without writing any SQL. Also, it can chack if 
   * specific row exists in database.
   * 
   * This class supports PKs over multiple fields
   * 
   * @package angie.library.database
   */
  abstract class DataObject {
    
    /**
     * Name of the table
     *
     * @var string
     */
    protected $table_name;
  	
  	/**
     * Array of field names
     *
     * @var array
     */
  	protected $fields;
  	
  	/**
     * Field map let us use special field names to point to existing fields. For 
     * instance, we can set that started_on maps to date_field_1 and it will do 
     * that automatically in getter and setters functions. 
     * 
     * $field_map = array(
     *   'started_on' => 'date_field_1'
     * )
     *
     * @var array
     */
  	protected $field_map = null;
  	
  	/**
     * Array of PK fields
     *
     * @var array
     */
  	protected $primary_key = array();
  	
  	/**
     * Name of autoincrement field (if exists)
     *
     * @var string
     */
  	protected $auto_increment = null;
  	
  	/**
     * List of protected fields (can't be set using setAttributes() method)
     *
     * @var array
     */
  	protected $protect = null;
  	
  	/**
     * List of accepted fields
     *
     * @var array
     */
  	protected $accept = null;

    /**
     * Return name of this model
     *
     * @param boolean $underscore
     * @param boolean $singular
     * @return string
     */
    abstract function getModelName($underscore = false, $singular = false);

    /**
     * Return object ID
     *
     * @return integer
     */
    abstract function getId();
  	
  	// ---------------------------------------------------
  	//  Internals, not overridable
  	// ---------------------------------------------------
  	
  	/**
     * Indicates if this is new object (not saved)
     *
     * @var boolean
     */
  	private $is_new = true;
  	
  	/**
     * This flag is set to true when data from row are inserted into fields
     *
     * @var boolean
     */
  	private $is_loading = false;
  	
  	/**
     * Field values
     *
     * @var array
     */
  	private $values = array();
  	
  	/**
     * Array of modified field values
     * 
     * Elements of this array are populated on setter call. Real name is 
     * resolved, old value is saved here (if exists) and new one is set. Keys 
     * used in this array are real field names only!
     *
     * @var array
     */
  	private $old_values = array();
  	
  	/**
     * Array of modified fiels
     *
     * @var array
     */
  	private $modified_fields = array();
  	
  	/**
     * Primary key is updated
     *
     * @var boolean
     */
  	private $primary_key_updated = false;
  	
  	/**
     * Construct data object and if $id is present load
     *
     * @param mixed $id
     */
  	function __construct($id = null) {
  	  if($id !== null) {
  	    $this->load($id);
  	  } // if
  	} // __construct
  	
  	/**
     * Validate object properties before object is saved 
     * 
     * This method is called before the item is saved and can be used to fetch 
     * errors in data before we really save it database. $errors is instance of 
     * ValidationErrors class that is used for error collection. If collection 
     * is empty object is considered valid and save process will continue
     *
     * @param ValidationErrors $errors
     */
  	function validate(ValidationErrors &$errors) {
  	  
  	} // validate
  	
  	/**
  	 * Returns true if $var is the same object this object is
  	 * 
  	 * Comparison is done on class - PK values for loaded objects, or as simple 
  	 * object comparison in case objects are not saved and loaded
  	 *
  	 * @param DataObject|mixed $var
     * @return boolean
  	 */
  	function is(&$var) {
      if($var instanceof DataObject) {
        if($this->isLoaded()) {
          return $var->isLoaded() && get_class($this) == get_class($var) && $this->getPrimaryKeyValue() == $var->getPrimaryKeyValue();
        } else {
          return $this == $var;
        } // if
      } // if

      return false;
  	} // is
  	
  	/**
     * Return object attributes
     * 
     * This function will return array of attribute name -> attribute value pairs 
     * for this specific project
     *
     * @return array
     */
  	function getAttributes() {
  	  $field_values = array();
  	  foreach($this->fields as $field) {
  	    $field_values[$field] = $this->getFieldValue($field);
  	  } // foreach
  	  
  	  return $field_values;
  	} // getAttributes
  	
  	/**
     * Set object attributes / properties. This function will take hash and set 
     * value of all fields that she finds in the hash
     *
     * @param array $attributes
     */
  	function setAttributes($attributes) {
  	  if(is_array($attributes)) {
  	    foreach($attributes as $k => $v) {
  	      if(is_array($this->protect) && (in_array($k, $this->protect) || in_array($k, $this->protect))) {
  	        continue; // field is in list of protected fields
  	      } // if
  	      if(is_array($this->accept) && !(in_array($k, $this->accept) || in_array($k, $this->protect))) {
  	        continue; // not in list of acceptable fields
  	      } // if
  	      if($this->fieldExists($k)) {
  	        $this->setFieldValue($k, $attributes[$k]);
  	      } // if
  	    } // foreach
  	  } // if
  	} // setAttributes
  	
  	/**
     * Return primary key columns
     *
     * @return array
     */
  	function getPrimaryKey() {
  	  return $this->primary_key;
  	} // getPrimaryKey
  	
  	/**
     * Return value of primary key
     *
     * @return array
     */
  	function getPrimaryKeyValue() {
      if($this->primary_key && count($this->primary_key)) {
        $ret = array();
        foreach($this->primary_key as $pk) {
          $ret[$pk] = $this->getFieldValue($pk);
        } // if
        return count($ret) > 1 ? $ret : $ret[$this->primary_key[0]];
      } // if

      return null;
  	} // getPrimaryKeyValue
  	
  	/**
     * Return value of table name
     *
     * @return string
     */
  	function getTableName() {
  	  return TABLE_PREFIX . $this->table_name;
  	} // getTableName
  	
  	// ---------------------------------------------------
  	//  CRUD methods
  	// ---------------------------------------------------
  	
  	/**
     * Load object by specific ID
     *
     * @param mixed $id
     * @return boolean
     * @throws InvalidParamError
     */
  	function load($id) {
      if($id) {
        $key = $this->getCacheKey(null, (integer) $id);

        $row = AngieApplication::cache()->isCached($key) ? AngieApplication::cache()->get($key) : null;

        if(empty($row)) {
          $fields = $this->getFields();
          $table_name = $this->getTableName();
          $where = $this->getWherePartById($id);

          $row = AngieApplication::cache()->get($key, function() use ($id, $fields, $table_name, $where) {
            return DB::executeFirstRow("SELECT " . implode(', ', $fields) . " FROM $table_name WHERE $where  LIMIT 0, 1");
          });
        } // if

        if(is_array($row)) {
          return $this->loadFromRow($row);
        } else {
          return false;
        } // if
      } else {
        throw new InvalidParamError('id', $id, '$id is expected to be a valid object ID');
      } // if
  	} // load
  	
  	/**
     * Load data from database row
     * 
     * If $cache_row is set to true row data will be added to cache
     *
     * @param array $row
     * @param boolean $cache_row
     * @return boolean
     * @throws InvalidParamError
     */
  	function loadFromRow($row, $cache_row = false) {
  	  if($row && is_array($row)) {
  	    $this->is_loading = true;
  	    
  	    foreach($row as $k => $v) {
  	      if($this->fieldExists($k)) {
  	        $this->setFieldValue($k, $v);
  	      } // if
  	    } // foreach
  	    
  	    if($cache_row) {
          AngieApplication::cache()->set($this->getCacheKey(null, (integer) $row['id']), $row);
  	    } // if
  	    
  	    $this->setLoaded(true);
  	    $this->is_loading = false;
  	    $this->resetModifiedFlags();
  	  } else {
  	    $this->is_loading = false;
  	    throw new InvalidParamError('row', $row, '$row is expected to be loaded database row');
  	  } // if

      return true;
  	} // loadFromRow
  	
  	/**
     * Save object into database (insert or update)
     * 
     * If this object does not pass validation error object with all model errors 
     * will be returned (object of ValidationErrors class)
     *
     * @return boolean
     * @throws DBQueryError
     * @throws ValidationErrors
     */
  	function save() {
  	  $errors = new ValidationErrors();
  	  $errors->setObject($this);

  	  EventsManager::trigger('on_before_object_validation', array(
  	    'object' => &$this,
  	  ));
  	  $this->validate($errors);

  	  EventsManager::trigger('on_after_object_validation', array(
  	    'object' => &$this,
  	    'errors' => &$errors,
  	  ));

  	  if($errors->hasErrors()) {
  	    throw $errors;
  	  } else {
  	    return $this->doSave();
  	  } // if
  	} // save
  	
  	/**
     * Delete specific object (and related objects if neccecery)
     *
     * @return boolean
     */
  	function delete() {
  		if($this->isLoaded()) {
        $cache_id = $this->getCacheKey();

        EventsManager::trigger('on_before_object_deleted', array('object' => &$this));

        $this->doDelete();
        $this->setNew(true);;
        $this->setLoaded(false);

        AngieApplication::cache()->remove($cache_id);
        EventsManager::trigger('on_object_deleted', array('object' => &$this));
  		} // if

      return true;
  	} // delete
  	
  	/**
     * Create a copy of this object and optionally save it
     *
     * @param boolean $save
     * @return DataObject
     */
    function copy($save = false) {
      $object_class = get_class($this);
      
      $copy = new $object_class();
      foreach($this->fields as $field) {
        if(!in_array($field, $this->primary_key)) {
          $copy->setFieldValue($field, $this->getFieldValue($field));
        } // if
      } // foreach
      
      if($save) {
        $copy->save();
      } // if
      
      return $copy;
    } // copy
  	
  	// ---------------------------------------------------
  	//  Flags
  	// ---------------------------------------------------
  	
  	/**
     * Return value of $is_new variable
     *
     * @return boolean
     */
  	function isNew() {
  	  return (boolean) $this->is_new;
  	} // isNew
  	
  	/**
     * Set new stamp value
     *
     * @param boolean $value New value
     */
  	function setNew($value) {
  	  $this->is_new = (boolean) $value;
  	} // setNew
  	
  	/**
     * Returns true if this object have row in database
     *
     * @return boolean
     */
  	function isLoaded() {
  	  return !$this->is_new;
  	} // isLoaded
  	
  	/**
     * Set loaded stamp value
     *
     * @param boolean $value New value
     */
  	function setLoaded($value) {
  	  $this->is_new = !$value;
  	} // setLoaded
  	
  	/**
  	 * Returns true if this object is in the middle of hydration process 
  	 * (loading values from database row)
  	 *
  	 * @return boolean
  	 */
  	function isLoading() {
  	  return $this->is_loading;
  	} // isLoading
  	
  	// ---------------------------------------------------
  	//  Fields
  	// ---------------------------------------------------
  	
  	/**
     * Return real field name
     * 
     * This function will return real field name. It will check if we have 
     * $field in field name map or in fields list and return appropriate value
     *
     * @param string $field
     * @return string
     */
  	function realFieldName($field) {
  		if(empty($this->field_map) || !isset($this->field_map[$field])) {
  			return $field;
  		} else {
  			return $this->field_map[$field];
  		} // if
  	} // realFieldName
  	
  	/**
     * Check if specific key is defined
     *
     * @param string $field Field name
     * @return boolean
     */
  	function fieldExists($field) {
  	  return in_array($this->realFieldName($field), $this->fields);
  	} // fieldExists
  	
  	/**
  	 * Return array of modified fields
  	 *
  	 * @return array
  	 */
  	function getModifiedFields() {
  	  return $this->modified_fields;
  	} // getModifiedFields
  	
  	/**
     * Check if this object has modified columns
     *
     * @return boolean
     */
  	function isModified() { 
  	  return (boolean) count($this->modified_fields);
  	} // isModified
  	
  	/**
     * Returns true if specific field is modified
     *
     * @param string $field
     * @return boolean
     */
  	function isModifiedField($field) {
  	  return in_array($this->realFieldName($field), $this->modified_fields);
  	} // isModifiedField

    /**
     * Revert field to old value
     *
     * @param $field
     */
    function revertField($field) {
      if ($this->isModifiedField($field)) {
        // revert field value
        $this->setFieldValue($field, $this->getOldFieldValue($field));

        // remove modified flag
        if(($key = array_search($field, $this->modified_fields)) !== false) {
          unset($this->modified_fields[$field]);
        }
      } // if
    } // revertField
  	
  	/**
     * Check if selected field is primary key
     *
     * @param string $field Field that need to be checked
     * @return boolean
     */
  	function isPrimaryKey($field) {
  	  return in_array($this->realFieldName($field), $this->primary_key);
  	} // isPrimaryKey
  	
  	/**
  	 * Return list of fields
  	 */
  	function getFields() {
  	  return $this->fields;
  	} // getFields
  	
  	/**
  	 * Calculate fields checksum
  	 * 
  	 * @return string
  	 */
  	function getFieldsChecksum() {
  		return md5(implode(' ', $this->values));
  	} // getFieldsChecksum
  	
  	/**
     * Return value of specific field and typecast it...
     *
     * @param string $field Field value
     * @param mixed $default Default value that is returned in case of any error
     * @return mixed
     */
  	function getFieldValue($field, $default = null) {
  	  return array_key_exists($this->realFieldName($field), $this->values) ? $this->values[$this->realFieldName($field)] : $default;
  	} // getFieldValue
  	
  	/**
  	 * Return old field values, before fields were updated
  	 *
  	 * @return array
  	 */
  	function getOldValues() {
  	  return $this->old_values;
  	} // getOldValues
  	
  	/**
  	 * Return all field value
  	 *
  	 * @param string $field
  	 * @return mixed
  	 */
  	function getOldFieldValue($field) {
  	  $real_field_name = $this->realFieldName($field);
  	  return isset($this->old_values[$real_field_name]) ? $this->old_values[$real_field_name] : null;
  	} // getOldFieldValue
  	
  	/**
     * Set specific field value
     * 
     * Set value of the $field. This function will make sure that everything 
     * runs fine - modifications are saved, in case of primary key old value 
     * will be remembered in case we need to update the row and so on
     *
     * @param string $field
     * @param mixed $value
     * @return mixed
     * @throws InvalidParamError
     */
    protected function setFieldValue($field, $value) {
  	  $real_field_name = $this->realFieldName($field);
  	  
  	  if(in_array($real_field_name, $this->fields)) {
  	    if(!isset($this->values[$real_field_name]) || ($this->values[$real_field_name] !== $value)) {
  		  
    		  // If we are loading object there is no need to remember if this field 
    		  // was modified, if PK has been updated and old value. We just skip that
    		  if(!$this->is_loading) {
    		    
    		    // Remember old value
      		  if(isset($this->values[$real_field_name])) {
      		    $old_value = $this->values[$real_field_name];
      		  } // if
    		  
      		  // Save primary key value. Also make sure that only the first PK value is
    			  // saved as old. Not to save second value on third modification ;)
    			  if($this->isPrimaryKey($real_field_name) && !isset($this->primary_key_updated[$real_field_name])) {
    			    if(!is_array($this->primary_key_updated)) {
    			      $this->primary_key_updated = array();
    			    } // if
    			    $this->primary_key_updated[$real_field_name] = true;
    			  } // if
    			  
    			  // Save old value if we haven't done that already
    			  if(isset($old_value) && !isset($this->old_values[$real_field_name])) {
    			    $this->old_values[$real_field_name] = $old_value;
    			  } // if
      		  
    			  // Remember that this file was modified
      		  $this->addModifiedField($real_field_name);
    		  } // if
    		  
  			  $this->values[$real_field_name] = $value;
    		} // if
    		
    		return $value;
  	  } else {
  	    throw new InvalidParamError('field', $field, "Field '$field' (mapped with '$real_field_name') does not exist");
  	  } // if
  	} // setFieldValue
  	
  	/**
     * Add new modified field
     *
     * @param string $field Field that need to be added
     */
  	function addModifiedField($field) {
  	  if(!in_array($field, $this->modified_fields)) {
  	    $this->modified_fields[] = $field;
  	  } // if
  	} // addModifiedField
  	
  	// ---------------------------------------------------
  	//  Database interaction
  	// ---------------------------------------------------
  	
  	/**
     * Check if specific row exists in database
     *
     * @param mixed $id
     * @return boolean
     */
  	function exists($id) {
  	  return (boolean) DB::executeFirstCell("SELECT count(*) AS 'row_count' FROM " . $this->getTableName() . " WHERE " . $this->getWherePartById($id));
  	} // exists
  	
  	/**
     * Save data into database
     *
     * @return integer or false
     */
  	function doSave() {
      EventsManager::trigger('on_before_object_save', array(
        'object' => &$this,
      ));

      // Insert...
      if($this->isNew()) {
        $this->doInsert();
      } else {
        $this->doUpdate();
      } // if

      EventsManager::trigger('on_after_object_save', array('object' => &$this));
      AngieApplication::cache()->removeByObject($this);

      return true;
  	} // doSave

    /**
     * Insert record in the database
     */
    private function doInsert() {
      EventsManager::trigger('on_before_object_insert', array('object' => &$this));

      DB::execute($this->getInsertSQL());

      if(($this->auto_increment !== null) && (!isset($this->values[$this->auto_increment]) || !$this->values[$this->auto_increment])) {
        $this->values[$this->auto_increment] = DB::lastInsertId();
      } // if
      $this->resetModifiedFlags();
      $this->setLoaded(true);

      EventsManager::trigger('on_object_inserted', array('object' => &$this));
    } // doInsert

    /**
     * Update database record
     */
    private function doUpdate() {
      EventsManager::trigger('on_before_object_update', array('object' => &$this));

      $sql = $this->getUpdateSQL();

      if($sql) {
        DB::execute($sql);

        $this->resetModifiedFlags();
        $this->setLoaded(true);

        EventsManager::trigger('on_object_updated', array('object' => &$this));
      } // if
    } // doUpdate
  	
  	/**
     * Delete object row from database
     *
     * @return boolean
     * @throws DBQueryError
     */
  	function doDelete() {
  	  return DB::execute("DELETE FROM " . $this->getTableName() . " WHERE " . $this->getWherePartById($this->getPrimaryKeyValue()));
  	} // doDelete
  	
  	/**
     * Prepare insert query
     *
     * @return string
     */
  	function getInsertSQL() {
  		$fields = array();
  		$values = array();
  		
  		// Any field value that is set and field exist is used in insert
  		foreach($this->values as $field_name => $field_value) {
  		  if($this->fieldExists($field_name)) {
  			  $fields[] = $field_name;
  			  $values[] = DB::escape($field_value);
  		  } // if
  		} // foreach
  		
  		// And put it all together
  		return sprintf("INSERT INTO %s (%s) VALUES (%s)", 
  		  $this->getTableName(), 
  		  implode(', ', $fields), 
  		  implode(', ', $values)
  		); // sprintf
  	} // getInsertSQL
  	
  	/**
     * Prepare update query
     *
     * @return string
     */
  	function getUpdateSQL() {
  		$fields = array();
  		
  		if(!count($this->modified_fields)) {
  		  return null;
  		} // if
  		
  		foreach($this->fields as $field_name) {
  			if($this->isModifiedField($field_name)) {
  			  $fields[] = $field_name . ' = ' . DB::escape($this->values[$field_name]);
  			} // if
  		} // foreach
  		
  		if(is_array($this->primary_key_updated)) {
  			$pks = $this->getPrimaryKey();
  			$old = array();
  			
  			foreach($pks as $pk) {
  			  $old[$pk] = isset($this->old_values[$pk]) ? $this->old_values[$pk] : $this->getFieldValue($pk);
  			} // foreach
  			
  			if(count($old) && $this->exists($old)) {
  			  return sprintf("UPDATE %s SET %s WHERE %s", $this->getTableName(), implode(', ', $fields), $this->getWherePartById($old));
  			} else {
  			  return $this->getInsertSQL();
  			} // if
  		} else {
  		  return sprintf("UPDATE %s SET %s WHERE %s", $this->getTableName(), implode(', ', $fields), $this->getWherePartById($this->getPrimaryKeyValue()));
  		} // if
  		
  	} // getUpdateSQL
  	
  	/**
     * Return where part of query
     *
     * @param mixed $value Array of values if we need them
     * @return string
     */
  	function getWherePartById($value = null) {
  	  $pks = $this->getPrimaryKey();
  	  
  	  if(count($pks) > 1) {
  	  	$where = array();
  	  	foreach($pks as $field) {
  	  	  $field_value = isset($value[$field]) ? $value[$field] : $this->getFieldValue($field);
  	  		$where[] = $field . ' = ' . DB::escape($field_value);
  	  	} // foreach
  	  	
  	  	return count($where) > 1 ? implode(' AND ', $where) : $where[0];
  	  } else {
  	    $pk = $pks[0];
  	    $pk_value = is_array($value) ? $value[$pk] : $value;
  	    return $pk . ' = ' . DB::escape($pk_value);
  	  } // if
  	} // getWherePartById
  	
  	/**
     * Reset modification idicators
     * 
     * Useful when you use setXXX functions but you dont want to modify
     * anything (just loading data from database in fresh object using 
     * setFieldValue function)
     */
  	function resetModifiedFlags() {
  	  $this->modified_fields = array();
  	  $this->old_values = array();
  	  $this->primary_key_updated = false;
  	} // resetModifiedFlags

    /**
     * Return cache key for this object)
     *
     * If we still don't have a lodaded object, we can pass a known ID to get the cache key
     *
     * @param array $subnamespace
     * @param integer $id
     * @return array
     */
    function getCacheKey($subnamespace = null, $id = null) {
      if($id === null) {
        return get_data_object_cache_key($this->getModelName(true), $this->getId(), $subnamespace);
      } else {
        return get_data_object_cache_key($this->getModelName(true), $id, $subnamespace);
      } // if
    } // getCacheKey
  	
  	// ---------------------------------------------------------------
  	//  Validators
  	// ---------------------------------------------------------------
  	
  	/**
     * Validates presence of specific field
     * 
     * In case of string value is trimmed and compared with the empty string. In 
     * case of any other type empty() function is used. If $min_value argument is 
     * provided value will also need to be larger or equal to it 
     * (validateMinValueOf validator is used)
     *
     * @param string $field Field name
     * @param mixed $min_value
     * @return boolean
     */
  	function validatePresenceOf($field, $min_value = null) {
  	  $value = $this->getFieldValue($field);
  	  if(is_string($value)) {
  	    if(trim($value)) {
  	      return $min_value === null ? true : $this->validateMinValueOf($field, $min_value);
  	    } else {
  	      return false;
  	    } // if
  	  } else {
        if(empty($value)) {
          return false;
        } else {
          return $min_value === null ? true : $this->validateMinValueOf($field, $min_value);
        } // if
  	  } // if
  	} // validatePresenceOf
  	
  	/**
     * This validator will return true if $value is unique (there is no row with such value in that field)
     *
     * @param string $field
     * @return boolean
     */
  	function validateUniquenessOf($field) {
  	  // Don't do COUNT(*) if we have one PK column
      $escaped_pk = is_array($pk_fields = $this->getPrimaryKey()) ? '*' : $pk_fields;
  	  
  	  // Get columns
  	  $fields = func_get_args();
  	  if(!is_array($fields) || count($fields) < 1) {
  	    return true;
  	  } // if
  	  
  	  // Check if we have existsing columns
  	  foreach($fields as $field) {
  	    if(!$this->fieldExists($field)) {
  	      return false;
  	    } // if
  	  } // foreach
  	  
  	  // Get where parets
  	  $where_parts = array();
  	  foreach($fields as $field) {
  	    $where_parts[] = $field . ' = ' . DB::escape($this->values[$field]);
  	  } // if
  	  
  	  // If we have new object we need to test if there is any other object
  	  // with this value. Else we need to check if there is any other EXCEPT
  	  // this one with that value
  	  if($this->isNew()) {
  	    $sql = sprintf("SELECT COUNT($escaped_pk) AS 'row_count' FROM %s WHERE %s", $this->getTableName(), implode(' AND ', $where_parts));
  	  } else {
  	    
  	    // Prepare PKs part...
  	    $pks = $this->getPrimaryKey();
  	    $pk_values = array();
  	    if(is_array($pks)) {
  	      foreach($pks as $pk) {
  	        if(isset($this->primary_key_updated[$pk]) && $this->primary_key_updated[$pk]) {
  	          $primary_key_value = $this->old_values[$pk];
  	        } else {
  	          $primary_key_value = $this->values[$pk];
  	        } // if
  	        $pk_values[] = sprintf('%s <> %s', $pk, DB::escape($primary_key_value));
  	      } // foreach
  	    } // if

  	    // Prepare SQL
  	    $sql = sprintf("SELECT COUNT($escaped_pk) AS 'row_count' FROM %s WHERE (%s) AND (%s)", $this->getTableName(), implode(' AND ', $where_parts), implode(' AND ', $pk_values));
  	  } // if
  	  
  	  return DB::executeFirstCell($sql) < 1;
  	} // validateUniquenessOf
  	
  	/**
     * Validate max value of specific field. If that field is string time 
     * max lenght will be validated
     *
     * @param string $field
     * @param integer $max
     * @return boolean
     */
    function validateMaxValueOf($field, $max) {
  	  if($this->fieldExists($field)) {
        $value = $this->getFieldValue($field);

        if(is_string($value) && !is_numeric($value)) {
          return strlen(trim($value)) <= $max;
        } else {
          return $value <= $max;
        } // if
      } else {
  	    return false;
  	  } // if
  	} // validateMaxValueOf
  	
  	/**
     * Valicate minimal value of specific field. 
     * 
     * If string minimal lenght is checked (string is trimmed before it is 
     * compared). In any other case >= operator is used
     *
     * @param string $field
     * @param integer $min Minimal value
     * @return boolean
     */
  	function validateMinValueOf($field, $min) {
  		if($this->fieldExists($field)) {
        $value = $this->getFieldValue($field);

        if(is_string($value) && !is_numeric($value)) {
          return strlen_utf(trim($value)) >= $min;
        } else {
          return $value >= $min;
        } // if
      } else {
  	    return false;
  	  } // if
  	} // validateMinValueOf
  	
  }