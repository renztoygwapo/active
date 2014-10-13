<?php

  /**
   * Foundation class of all Angie exceptions
   * 
   * @package angie
   */
  class Error extends Exception implements IDescribe, IJSON {
    
    /**
     * Error message
     *
     * @var string
     */
    protected $message;
  
    /**
     * Error line
     *
     * @var integer
     */
    protected $line;
    
    /**
     * Error file
     *
     * @var string
     */
    protected $file;
    
    /**
     * Additional error parameters
     *
     * @var array
     */
    protected $additional = null;
    
    /**
     * Construct error object
     *
     * @param string $message
     * @param array $additional
     */
    function __construct($message, $additional = null) {
      $this->additional = $additional;
      parent::__construct($message);
    } // __construct
    
    /**
     * Return error params (name -> value pairs)
     * 
     * General params are file and line where error was thrown. Subclasses may 
     * have their own error parameters
     *
     * @return array
     */
    function getParams() {
      return is_array($this->additional) && count($this->additional) ? $this->additional : array();
    } // getParams

    /**
     * Return specific parameter
     *
     * @param string $name
     * @return mixed
     */
    function getParam($name) {
      return array_var($this->additional, $name);
    } // getParam
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Convert current object to JSON
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return string
     */
    function toJSON(IUser $user, $detailed = false, $for_interface = false) {
      return JSON::encode($this->describe($user, $detailed, $for_interface));
    } // toJSON
    
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
      return array_merge(array(
        'type' => get_class($this), 
        'message' => $this->getMessage(), 
        'file' => $this->getFile(), 
        'line' => $this->getLine(), 
        'trace' => AngieApplication::isInDebugMode() || AngieApplication::isInDevelopment() ? $this->getTraceAsString() : null, 
      ), $this->getParams());
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      return array_merge(array(
        'type' => get_class($this),
        'message' => $this->getMessage(),
        'file' => $this->getFile(),
        'line' => $this->getLine(),
        'trace' => AngieApplication::isInDebugMode() || AngieApplication::isInDevelopment() ? $this->getTraceAsString() : null,
      ), $this->getParams());
    } // describeForApi
  
  }