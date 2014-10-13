<?php

  /**
   * Collection of named data
   * 
   * @package angie.library
   */
  class NamedList implements IteratorAggregate, ArrayAccess, Countable {
    
    /**
     * List data
     * 
     * @var array
     */
    protected $data = array();
    
    /**
     * All data only to be appended to the list
     *
     * @var boolean
     */
    protected $append_only = false;
    
    /**
     * Set to true if prepareItem() function needs to be called when item value 
     * is being set (false by defualt)
     *
     * @var boolean
     */
    protected $prepare_items = false;
    
    /**
     * Construct named list
     *
     * @param array $data
     */
    function __construct($data = null) {
      if($data !== null && is_foreachable($data)) {
        foreach($data as $k => $v) {
          $this->add($k, $v);
        } // foreach
      } // if
    } // __construct
    
    // ---------------------------------------------------
    //  Public interface
    // ---------------------------------------------------
    
    /**
     * Return true if $name entry exists in this list
     * 
     * @param string $name
     * @return boolean
     */
    function exists($name) {
      return isset($this->data[$name]);
    } // exists
    
    /**
     * Return item with $name
     *
     * @param string $name
     * @return mixed
     */
    function get($name) {
    	return isset($this->data[$name]) ? $this->data[$name] : null;
    } // get
    
    /**
     * Add data to the list
     * 
     * $name can be string in which case system sets $data as value. If $name is 
     * array, system will add multiple values, where name is key and value is 
     * value of given element
     *
     * @param string $name
     * @param mixed $data
     * @param boolean $skip_existing
     * @return mixed
     */
    function add($name, $data = null, $skip_existing = false) {
      if(is_array($name)) {
        foreach($name as $k => $v) {
          if($skip_existing && isset($this->data[$k])) {
            continue;
          } // if
          
          $this->doAdd($k, $v);
        } // foreach
        
        return $name;
      } else {
        if(isset($this->data[$name])) {
          return $skip_existing ? $this->data[$name] : $this->doAdd($name, $data);
        } else {
          return $this->doAdd($name, $data);
        } // if
      } // if
    } // add
    
    /**
     * Add data to the beginning of the list
     *
     * @param string $name
     * @param mixed $data
     * @param boolean $skip_existing
     * @return mixed
     * @throws NotImplementedError
     */
    function beginWith($name, $data, $skip_existing = true) {
      if($this->append_only) {
        throw new NotImplementedError(__METHOD__);
      } // if
      
      if($skip_existing && isset($this->data[$name])) {
        return $this->data[$name];
      } // if
      
      return $this->doAdd($name, $data, array('begin_with' => true));
    } // beginWith
    
    /**
     * Add data before $before element
     *
     * @param string $name
     * @param mixed $data
     * @param string $before
     * @param boolean $skip_existing
     * @return mixed
     * @throws NotImplementedError
     */
    function addBefore($name, $data, $before, $skip_existing = false) {
      if($this->append_only) {
        throw new NotImplementedError(__METHOD__);
      } // if
      
      if($skip_existing && isset($this->data[$name])) {
        return $this->data[$name];
      } // if
      
      return $this->doAdd($name, $data, array('before' => $before));
    } // addBefore
    
    /**
     * Add item after $after list element
     *
     * @param string $name
     * @param mixed $data
     * @param string $after
     * @param boolean $skip_existing
     * @return mixed
     * @throws NotImplementedError
     */
    function addAfter($name, $data, $after, $skip_existing = false) {
      if($this->append_only) {
        throw new NotImplementedError(__METHOD__);
      } // if
      
      if($skip_existing && isset($this->data[$name])) {
        return $this->data[$name];
      } // if
      
      return $this->doAdd($name, $data, array('after' => $after));
    } // addAfter
    
    /**
     * Remove data from the list
     *
     * @param string $name
     */
    function remove($name) {
      if(is_array($name)) {
        foreach($name as $k) {
          if (isset($this->data[$k])) {
            unset($this->data[$k]);
          } //if
        } // foreach
      } else {
        if (isset($this->data[$name])) {
          unset($this->data[$name]);
        } //if
      } //if
    } // remove
    
    /**
     * Clear the list
     */
    function clear() {
      $this->data = array();
    } // clear
    
    /**
     * Return all data keys
     *
     * @return array
     */
    function keys() {
      return array_keys($this->data);
    } // keys
    
    /**
     * return named list as array
     *
     * @return array
     */
    function toArray() {
      return $this->data;
    } // toArray

    /**
     * Sort with a callback
     *
     * @param Closure $callback
     * @throws InvalidInstanceError
     */
    function sort($callback) {
      if($callback instanceof Closure) {
        uasort($this->data, $callback);
      } else {
        throw new InvalidInstanceError('callback', $callback, 'Closure');
      } // if
    } // sort
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
    
    /**
     * Do add item to the list
     * 
     * @param string $name
     * @param mixed $data
     * @param mixed $options
     * @return mixed
     */
    protected function doAdd($name, $data, $options = null) {
      
      // Add data to the beginning of the list
      if($options && isset($options['begin_with'])) {
        $new_data = array($name => ($this->prepare_items ? $this->prepareItem($data) : $data));
      
        foreach($this->data as $k => $v) {
          $new_data[$k] = $v;
        } // foreach
        
        $this->data = $new_data;
        
      // Add data before given item
      } elseif($options && isset($options['before'])) {
        
        $new_data = array();
        $added = false;
        
        foreach($this->data as $k => $v) {
          if($k == $options['before']) {
            $new_data[$name] = $this->prepare_items ? $this->prepareItem($data) : $data;
            $added = true;
          } // if
          
          $new_data[$k] = $v;
        } // foreach
        
        if(!$added) {
          $new_data[$name] = $this->prepare_items ? $this->prepareItem($data) : $data;
        } // if
        
        $this->data = $new_data;
        
      // Add after given item
      } elseif($options && isset($options['after'])) {
        
        $new_data = array();
        $added = false;
        
        foreach($this->data as $k => $v) {
          $new_data[$k] = $v;
          
          if($k == $options['after']) {
            $new_data[$name] = $this->prepare_items ? $this->prepareItem($data) : $data;
            $added = true;
          } // if
        } // foreach
        
        if(!$added) {
          $new_data[$name] = $this->prepare_items ? $this->prepareItem($data) : $data;
        } // if
        
        $this->data = $new_data;
        
      // Append
      } else {
        $this->data[$name] = $this->prepare_items ? $this->prepareItem($data) : $data;
      } // if
      
      return $this->data[$name];
    } // doAdd
    
    /**
     * Prepare item value
     * 
     * This function is called for each value when prepare_value flag is set to 
     * true for this particular list 
     * 
     * @param mixed $value
     * @return mixed
     */
    protected function prepareItem($value) {
      return $value;
    } // prepareItem
    
    // ---------------------------------------------------
    //  Array access
    // ---------------------------------------------------
    
    /**
     * Check if $offset exists
     *
     * @param string $offset
     * @return boolean
     */
    function offsetExists($offset) {
      return isset($this->data[$offset]);
    } // offsetExists
    
    /**
     * Return value at $offset
     *
     * @param string $offset
     * @return mixed
     */
 	  function offsetGet($offset) {
 	    return $this->data[$offset];
 	  } // offsetGet
 	  
 	  /**
 	   * Set value at $offset
 	   *
 	   * @param string $offset
 	   * @param mixed $value
 	   */
 	  function offsetSet($offset, $value) {
 	    $this->data[$offset] = $value;
 	  } // offsetSet
 	  
 	  /**
 	   * Unset value at $offset
 	   *
 	   * @param string $offset
 	   */
 	  function offsetUnset($offset) {
 	    unset($this->data[$offset]);
 	  } // offsetUnset
 	  
 	  /**
 	   * Number of elements
 	   *
 	   * @return integer
 	   */
 	  function count() {
 	    return count($this->data);
 	  } // count
 	  
 	  /** 
     * Returns an iterator for for this object, for use with foreach 
     * 
     * @return ArrayIterator 
     */ 
     function getIterator() { 
       return new ArrayIterator($this->data); 
     } // getIterator
    
  }