<?php

  /**
   * Abstraction of database query result
   *
   * @package angie.library.database
   */
  abstract class DBResult implements IteratorAggregate, ArrayAccess, Countable, IDescribe, IJSON {
    
    // Casting modes
    const CAST_INT = 'int';
    const CAST_FLOAT = 'float';
    const CAST_STRING = 'string';
    const CAST_BOOL = 'bool';
    const CAST_DATE = 'date';
    const CAST_DATETIME = 'datetime';
    
    /**
     * Cursor position
     *
     * @var integer
     */
    protected $cursor_position = 0;
    
    /**
     * Current row, set by 
     *
     * @var integer
     */
    protected $current_row;
    
    /**
     * Database result resource
     *
     * @var resource
     */
    protected $resource;
    
    /**
     * Return mode
     *
     * @var integer
     */
    protected $return_mode;
    
    /**
     * Name of the class or field for return, if this result is returning 
     * objects based on rows
     *
     * @var string
     */
    protected $return_class_or_field;
    
    /**
     * Field casting rules
     *
     * @var array
     */
    protected $casting = array();
    
    /**
     * Construct DBResult from resource
     *
     * @param resource $resource
     * @param integer $return_mode
     * @param string $return_class_or_field
     * @return DBResult
     * @throws InvalidParamError
     */
    function __construct($resource, $return_mode = DB::RETURN_ARRAY, $return_class_or_field = null) {
      if(!is_resource($resource)) {
        throw new InvalidParamError('resource', $resource, '$resource is expected to be valid DB result resource');
      } // if
      
      $this->resource = $resource;
      $this->return_mode = $return_mode;
      $this->return_class_or_field = $return_class_or_field;
    } // __construct
    
    /**
     * Free result on destruction
     */
    function __destruct() {
      $this->free();
    } // __destruct
    
    /**
     * Return resource
     * 
     * @return resource
     */
    function getResource() {
    	return $this->resource;
    } // getResource
    
    /**
     * Set cursor to given row
     *
     * @param integer $row_num
     */
    public abstract function seek($row_num);
    
    /**
     * Return row at $row_num
     * 
     * This function loads row at given position. When row is loaded, cursor is 
     * set for the next row
     *
     * @param integer $row_num
     * @return mixed
     */
    public function getRowAt($row_num) {
      if($this->seek($row_num)) {
        $this->next();
        return $this->getCurrentRow();
      } // if
      
      return null;
    } // getRowAt
    
    /**
     * Return next record in result set
     *
     * @return array
     */
    //public abstract function next();
    
    /**
     * Free resource when we are done with this result
     *
     * @return boolean
     */
    public abstract function free();
    
    /**
     * Return cursor position
     *
     * @return integer
     */
    public function getCursorPosition() {
      return $this->cursor_position;
    } // getCursorPosition
    
    /**
     * Return current row
     *
     * @return mixed
     */
    public function getCurrentRow() {
      return $this->current_row;
    } // getCurrentRow
    
    /**
     * Set current row
     *
     * @param array $row
     */
    protected function setCurrentRow($row) {
      switch($this->return_mode) {
        
        // Set object based on class name that we got in constructor
        case DB::RETURN_OBJECT_BY_CLASS:
          $class_name = $this->return_class_or_field;
          
          $this->current_row = new $class_name();
          $this->current_row->loadFromRow($row);
          break;
          
        // Set object based on class name from field
        case DB::RETURN_OBJECT_BY_FIELD:
          $class_name = $row[$this->return_class_or_field];
          
          $this->current_row = new $class_name();
          $this->current_row->loadFromRow($row);
          break;
          
        // Just return array
        default:
          $this->current_row = $row;
          
          if(!empty($this->casting)) {
            foreach($this->current_row as $k => $v) {
              $this->current_row[$k] = $this->cast($k, $v);
            } // foreach
          } // if
      } // if
    } // setCurrentRow
    
    /**
     * Return array of all rows
     *
     * @return array
     */
    function toArray() {
      $result = array();
      
      foreach($this as $row) {
        $result[] = $row;
      } // foreach
      
      return $result;
    } // toArray
    
    /**
     * Returns DBResult indexed by value of a field or by result of specific 
     * getter method
     * 
     * This function will treat $field_or_getter as field in case or array 
     * return method, or as getter in case of object return method
     *
     * @param string $field_or_getter
     * @return array
     */
    function toArrayIndexedBy($field_or_getter) {
      $result = array();
      
      foreach($this as $row) {
        if($this->return_mode == DB::RETURN_ARRAY) {
          $result[$row[$field_or_getter]] = $row;
        } else {
          $result[$row->$field_or_getter()] = $row;
        } // if
      } // foreach
      
      return $result;
    } // toArrayIndexedBy
    
    // ---------------------------------------------------
    //  Angie interface implementations
    // ---------------------------------------------------
    
    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      if($this->count()) {
        $records = array();
    	  
      	foreach($this as $record) {
          $records[] = $record instanceof IDescribe ? $record->describe($user, $detailed, $for_interface) : $record;
      	} // foreach
      	
      	return $records;
      } else {
        return null;
      } // if
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      if($this->count()) {
        $records = array();

        foreach($this as $record) {
          $records[] = $record instanceof IDescribe ? $record->describeForApi($user, $detailed) : $record;
        } // foreach

        return $records;
      } else {
        return null;
      } // if
    } // describeForApi
    
    /**
     * Forward content of this result as JSON
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return string
     */
    function toJSON(IUser $user, $detailed = false, $for_interface = false) {
    	if($this->count()) {
    	  $records = array();
    	  
      	foreach($this as $record) {
      		$records[] = JSON::encode($record, $user, $detailed, $for_interface);
      	} // foreach
      	
      	return '[' . implode(',', $records) . ']';
    	} else {
    	  return '[]';
    	} // if
    } // toJSON
    
    // ---------------------------------------------------
    //  Casting
    // ---------------------------------------------------
    
    /**
     * Set casting options
     * 
     * Several options are possible:
     * 
     * // Set casting for a signle field
     * $result->setCasting('company_id', DBResult::CAST_INT);
     * 
     * // Set casting for multiple fields
     * $result->setCasting(array(
     *   'company_id' => DBResult::CAST_INT,
     *   'created_on' => DBResult::CAST_DATE,
     * )); 
     * 
     * // Reset casting settings for specific field
     * $result->setCasting('company_id', null);
     * 
     * // Reset casting settings for multiple fields
     * $result->setCasting(array(
     *   'company_id' => null,
     *   'created_on' => null,
     * )); 
     * 
     * // Reset casting for all fields
     * $result->setCastign(null);
     * 
     * @param string|array $field
     * @param mixed $cast
     */
    function setCasting($field, $cast = null) {
      if(is_array($field)) {
        foreach($field as $k => $v) {
          if($v === null) {
            if(isset($this->casting[$k])) {
              unset($this->casting[$k]);
            } // if
          } else {
            $this->casting[$k] = $v;
          } // if
        } // if
      } elseif($field === null) {
        $this->casting = array();
      } else {
        if($cast === null) {
          if(isset($this->casting[$field])) {
            unset($this->casting[$field]);
          } // if
        } else {
          $this->casting[$field] = $cast;
        } // if
      } // if
    } // setCasting
    
    /**
     * Cast field value to proper value
     * 
     * If $value is NULL, it will always be returned as NULL. If no casting 
     * settings exist for the field, original $value will be returned
     * 
     * @param string $filed
     * @param mixed $value
     * @return mixed
     */
    protected function cast($field, $value) {
      if(empty($this->casting) || $value === null || !isset($this->casting[$field])) {
        return $value;
      } else {
        if($this->casting[$field] instanceof Closure) {
          return $this->casting[$field]->__invoke($value);
        } else {
          switch($this->casting[$field]) {
            case self::CAST_INT:
              return (int) $value;
            case self::CAST_FLOAT:
              return (float) $value;
            case self::CAST_STRING:
              return (string) $value;
            case self::CAST_BOOL:
              return (bool) $value;
            case self::CAST_DATE:
              return new DateValue($value);
            case self::CAST_DATETIME:
              return new DateTimeValue($value);
            default:
              return $value;
          } // switch
        } // if
      } // if
    } // cast
    
    // ---------------------------------------------------
    //  Return mode
    // ---------------------------------------------------
    
    /**
     * Set result to return objects by class name
     *
     * @param string $class_name
     */
    function returnObjectsByClass($class_name) {
      $this->return_mode = DB::RETURN_OBJECT_BY_CLASS;
      
      $this->return_class_or_field = $class_name;
    } // returnObjectsByClass
    
    /**
     * Set result to load objects of class based on filed value
     *
     * @param string $field_name
     */
    function returnObjectsByField($field_name) {
      $this->return_mode = DB::RETURN_OBJECT_BY_FIELD;
      
      $this->return_class_or_field = $field_name;
    } // returnObjectsByField
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Check if $offset exists
     *
     * @param string $offset
     * @return boolean
     */
    function offsetExists($offset) {
      return $offset >= 0 && $offset < $this->count();
    } // offsetExists
    
    /**
     * Return value at $offset
     *
     * @param string $offset
     * @return mixed
     */
 	  function offsetGet($offset) {
 	    return $this->getRowAt($offset);
 	  } // offsetGet
 	  
 	  /**
 	   * Set value at $offset
 	   *
 	   * @param string $offset
 	   * @param mixed $value
 	   * @return null
 	   */
 	  function offsetSet($offset, $value) {
 	    throw new NotImplementedError(__CLASS__ . '::' . __METHOD__, 'DB results are read only!');
 	  } // offsetSet
 	  
 	  /**
 	   * Unset value at $offset
 	   *
 	   * @param string $offset
 	   * @return null
 	   */
 	  function offsetUnset($offset) {
 	    throw new NotImplementedError(__CLASS__ . '::' . __METHOD__, 'DB results are read only!');
 	  } // offsetUnset
    
    /**
 	   * Number of elements
 	   *
 	   * @return integer
 	   * @throws NotImplementedError
 	   */
 	  function count() {
 	    throw new NotImplementedError(__CLASS__ . '::' . __METHOD__, 'count() method needs to be overriden in child classes');
 	  } // count
 	  
 	  /** 
     * Returns an iterator for for this object, for use with foreach 
     * 
     * @return ArrayIterator 
     */ 
     function getIterator() { 
       return new DBResultIterator($this);
     } // getIterator
    
  }