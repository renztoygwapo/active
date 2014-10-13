<?php

  /**
   * Access log helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class IAccessLogImplementation {
  
    /**
     * Parent object
     *
     * @var IAccessLog
     */
    protected $object;
    
    /**
     * Construct access log helper instance
     * 
     * @param IAccessLog $object
     */
    function __construct(IAccessLog $object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Returns true if parnet object was accessed by a given user since given 
     * date and time value
     * 
     * @param IUser $by
     * @param DateTimeValue $since
     * @return boolean
     */
    function isAccessedSince(IUser $by, DateTimeValue $since) {
      return AccessLogs::isAccessedSince($this->object, $by, $since);
    } // isAccessedSince
    
    /**
     * Register access to the log
     * 
     * @param IUser $by
     */
    function log(IUser $by) {
      AccessLogs::log($this->object, $by);

      if($by instanceof User && AngieApplication::isFrameworkLoaded('notifications')) {
        Notifications::markReadByParent($this->object, $by);
      } // if
    } // log

    /**
     * Register access of anonymous user to the log
     */
    function logAnonymous() {
      AccessLogs::logAnonymous($this->object);
    } // logAnonymous
    
    /**
     * Register download
     * 
     * @param IUser $by
     */
    function logDownload(IUser $by) {
      AccessLogs::logDownload($this->object, $by);
    } // logDownload

    /**
     * Register download of anonymous user to the log
     */
    function logAnonymousDownload() {
      AccessLogs::logAnonymousDownload($this->object);
    } // logAnonymousDownload

	  /**
	   * Get all logs for given object
	   */
	  function getAll() {
		  return AccessLogs::getAll($this->object);
	  } // getAll

  }