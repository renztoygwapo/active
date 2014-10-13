<?php

  /**
   * Single date time value
   *
   * This class provides some handy methods for working with timestamps and extracting data from them
   *
   * @package angie.library.datetime
   */
  class DateTimeValue extends DateValue {
    
    /**
     * Cached hour value
     *
     * @var integer
     */
    protected $hour;
    
    /**
     * Cached minutes value
     *
     * @var integer
     */
    protected $minute;
    
    /**
     * Cached seconds value
     *
     * @var integer
     */
    protected $second;
    
    // ---------------------------------------------------
    //  Static methods
    // ---------------------------------------------------
    
    /**
     * Returns current time object
     *
     * @return DateTimeValue
     */
    static function now() {
      return new DateTimeValue(time());
    } // now
    
    /**
     * This function works like mktime, just it always returns GMT
     *
     * @param integer $hour
     * @param integer $minute
     * @param integer $second
     * @param integer $month
     * @param integer $day
     * @param integer $year
     * @return DateTimeValue
     */
    static function make($hour, $minute, $second, $month, $day, $year) {
      return new DateTimeValue(mktime($hour, $minute, $second, $month, $day, $year));
    } // make

    /**
     * Make instance from timestamp
     *
     * @param integer $timestamp
     * @return DateTimeValue
     */
    static function makeFromTimestamp($timestamp) {
      return new DateTimeValue($timestamp);
    } // makeFromTimestamp
    
    /**
     * Make time from string using strtotime() function. This function will return null
     * if it fails to convert string to the time
     *
     * @param string $str
     * @return DateTimeValue
     */
    static function makeFromString($str) {
      $timestamp = strtotime($str);
      return ($timestamp === false) || ($timestamp === -1) ? null : new DateTimeValue($timestamp);
    } // makeFromString

    /**
     * Return beginning of the month DateTimeValue
     *
     * @param integer $month
     * @param integer $year
     * @return DateTimeValue
     */
    static function beginningOfMonth($month, $year) {
      return new DateTimeValue("$year-$month-1 00:00:00");
    } // beginningOfMonth

    /**
     * Return end of the month
     *
     * @param integer $month
     * @param integer $year
     * @return DateTimeValue
     */
    static function endOfMonth($month, $year) {
      $reference = mktime(0, 0, 0, $month, 15, $year);
      $last_day = date('t', $reference);

      return new DateTimeValue("$year-$month-$last_day 23:59:59");
    } // endOfMonth

    /**
     * Return valid DateValue offset by given user's time zone
     *
     * @param IUser $user
     * @return DateValue
     */
    function getForUser($user = null) {
      if($user instanceof IUser) {
        return new DateTimeValue($this->getTimestamp() + get_user_gmt_offset($user));
      } else {
        return clone($this);
      } // if
    } // getForUser
    
    /**
     * Return valid DateTimeValue offset by given user's time zone in GMT
     *
     * @param IUser $user
     * @return DateValue
     */
    function getForUserInGMT($user = null) {
      if($user instanceof IUser) {
        return new DateTimeValue($this->getTimestamp() - get_user_gmt_offset($user));
      } else {
        return clone($this);
      } // if
    } // getForUser
    
    // ---------------------------------------------------
    //  Formating
    // ---------------------------------------------------
    
    /**
     * Format date for user
     * 
     * @param IUser $user
     * @param integer $offset
     * @return string
     */
    function formatDateForUser($user = null, $offset = null) {
    	return parent::formatForUser($user, $offset);
    } // formatDateForUser

    /**
     * Format value for given user
     *
     * @param IUser $user
     * @param integer $offset
     * @return string
     */
    function formatForUser($user = null, $offset = null) {
      $user = $user instanceof IUser ? $user : Authentication::getLoggedUser();
      
      if($user instanceof IUser) {
        $format = $user->getDateTimeFormat();
      } else {
        $format = FORMAT_DATETIME;
      } // if
      
      return $this->__formatForUser($format, ($offset === null ? get_user_gmt_offset($user) : (integer) $offset));
    } // formatForUser
    
    /**
     * Format time for user
     *
     * @param IUser $user
     * @param integer $offset
     * @return string
     */
    function formatTimeForUser($user = null, $offset = NULL) {
      $user = $user instanceof IUser ? $user : Authentication::getLoggedUser();
      
      if($user instanceof IUser) {
      	$format = $user->getTimeFormat();
      } else {
        $format = FORMAT_TIME;
      } // if
      
      return $this->__formatForUser($format, ($offset === null ? get_user_gmt_offset($user) : (integer) $offset));
    } // formatTimeForUser
    
    /**
     * Return datetime formated in MySQL datetime format
     *
     * @return string
     */
    function toMySQL() {
      return $this->format(DATETIME_MYSQL);
    } // toMySQL

    /**
     * Return padded hour value
     *
     * @return string
     */
    function paddedHour() {
      return str_pad($this->getHour(), 2, '0');
    } // paddedHour

    /**
     * Return padded minute value
     *
     * @return string
     */
    function paddedMinute() {
      return str_pad($this->getMinute(), 2, '0');
    } // paddedMinute

    /**
     * Return padded seconds value
     *
     * @return string
     */
    function paddedSecond() {
      return str_pad($this->getSecond(), 2, '0');
    } // paddedSecond
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
    
    /**
     * Break timestamp into its parts and set internal variables
     */
    function parse() {
      $this->date_data = getdate($this->timestamp);
      
      if($this->date_data) {
        $this->year   = (integer) $this->date_data['year'];
        $this->month  = (integer) $this->date_data['mon'];
        $this->day    = (integer) $this->date_data['mday'];
        $this->hour   = (integer) $this->date_data['hours'];
        $this->minute = (integer) $this->date_data['minutes'];
        $this->second = (integer) $this->date_data['seconds'];
      } // if
    } // parse
    
    /**
     * Update internal timestamp based on internal param values
     */
    function setTimestampFromAttributes() {
      $this->setTimestamp(mktime(
        $this->hour,
        $this->minute,
        $this->second,
        $this->month,
        $this->day,
        $this->year
      )); // setTimestamp
    } // setTimestampFromAttributes
    
    // ---------------------------------------------------
    //  Interface implementations
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
      $result = parent::describe($user, $detailed, $for_interface);
      
      $result['class'] = 'DateTimeValue';
      
      $result['formatted_date'] = $this->formatDateForUser($user);
      $result['formatted_date_gmt'] = $this->formatDateForUser($user, 0);
       
      $result['formatted_time'] = $this->formatTimeForUser($user);  
      $result['formatted_time_gmt'] = $this->formatTimeForUser($user, 0); 
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      return $this->toMySQL();
    } // describeForApi
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return hour
     *
     * @return integer
     */
    function getHour() {
      return $this->hour;
    } // getHour
    
    /**
     * Set hour value
     *
     * @param integer $value
     */
    function setHour($value) {
      $this->hour = (integer) $value;
      $this->setTimestampFromAttributes();
    } // setHour
    
    /**
     * Return minute
     *
     * @return integer
     */
    function getMinute() {
      return $this->minute;
    } // getMinute
    
    /**
     * Set minutes value
     *
     * @param integer $value
     */
    function setMinute($value) {
      $this->minute = (integer) $value;
      $this->setTimestampFromAttributes();
    } // setMinute
    
    /**
     * Return seconds
     *
     * @return integer
     */
    function getSecond() {
      return $this->second;
    } // getSecond
    
    /**
     * Set seconds
     *
     * @param integer $value
     */
    function setSecond($value) {
      $this->second = (integer) $value;
      $this->setTimestampFromAttributes();
    } // setSecond
  
  }