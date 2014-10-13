<?php

  /**
   * Data value object
   *
   * Instance of this class represents single date (time part is ignored)
   *
   * @package angie.library.datetime
   */
  class DateValue implements IDescribe, IJSON {

    /**
     * Internal timestamp value
     *
     * @var integer
     */
    protected $timestamp;

    /**
     * Cached day value
     *
     * @var integer
     */
    protected $day;

    /**
     * Cached month value
     *
     * @var integer
     */
    protected $month;

    /**
     * Cached year value
     *
     * @var integer
     */
    protected $year;

    /**
     * Date data, result of getdate() function
     *
     * @var array
     */
    protected $date_data;

    // ---------------------------------------------------
    //  Static methods
    // ---------------------------------------------------

    /**
     * Returns today object
     *
     * @return DateValue
     */
    static function now() {
      return new DateValue(time());
    } // now

    /**
     * This function works like mktime, just it always returns GMT
     *
     * @param integer $month
     * @param integer $day
     * @param integer $year
     * @return DateValue
     */
    static function make($month, $day, $year) {
      return new DateValue(mktime(0, 0, 0, $month, $day, $year));
    } // make

    /**
     * Make instance from timestamp
     *
     * @param integer $timestamp
     * @return DateValue
     */
    static function makeFromTimestamp($timestamp) {
      return new DateValue($timestamp);
    } // makeFromTimestamp

    /**
     * Make time from string using strtotime() function. This function will
     * return null if it fails to convert string to the time
     *
     * @param string $str
     * @return DateValue
     */
    static function makeFromString($str) {
      $timestamp = strtotime($str);
      return ($timestamp === false) || ($timestamp === -1) ? null : new DateValue($timestamp);
    } // makeFromString

    /**
     * Return beginning of the month DateTimeValue
     *
     * @param integer $month
     * @param integer $year
     * @return DateValue
     */
    static function beginningOfMonth($month, $year) {
      return new DateValue("$year-$month-1 00:00:00");
    } // beginningOfMonth

    /**
     * Return end of the month
     *
     * @param integer $month
     * @param integer $year
     * @return DateValue
     */
    static function endOfMonth($month, $year) {
      $reference = mktime(0, 0, 0, $month, 15, $year);
      $last_day = date('t', $reference);

      return new DateValue("$year-$month-$last_day");
    } // endOfMonth

    /**
     * Loop through weeks from $from date to $to date and call $callback with $from_date, $to_date, $year and $week
     * parameters
     *
     * @param DateValue $from
     * @param DateValue $to
     * @param Closure $callback
     * @param integer $first_week_day
     */
    static function iterateWeekly(DateValue $from, DateValue $to, $callback, $first_week_day = 0) {
      $start_from = $from->beginningOfWeek($first_week_day);
      $to_the_end = $to->endOfWeek($first_week_day);

      foreach (new DatePeriod(new DateTime($start_from->toMySQL()), new DateInterval('P1W'), new DateTime($to_the_end->toMySQL())) as $date) {
        $week_start = new DateTimeValue($date->getTimestamp(), $first_week_day);
        $week_end = $week_start->endOfWeek($first_week_day);

        $callback->__invoke($week_start, $week_end);
      } // foreach
    } // iterateWeekly

    // ---------------------------------------------------
    //  Instance methods
    // ---------------------------------------------------

    /**
     * Construct the DateValue
     *
     * @param integer $timestamp
     */
    function __construct($timestamp = null) {
      if($timestamp === null) {
        $timestamp = time();
      } elseif(is_string($timestamp)) {
        $timestamp = strtotime($timestamp);
      } // if
      $this->setTimestamp($timestamp);
    } // __construct

    /**
     * Advance for specific time
     *
     * If $mutate is true value of this object will be changed. If false a new
     * DateValue or DateTimeValue instance will be returned with timestamp
     * moved for $input number of seconds
     *
     * @param integer $input
     * @param boolean $mutate
     * @return DateTimeValue
     */
    function advance($input, $mutate = true) {
      $timestamp = (integer) $input;

      if($mutate) {
        $this->setTimestamp($this->getTimestamp() + $timestamp);
      } else {
        if($this instanceof DateTimeValue) {
          return new DateTimeValue($this->getTimestamp() + $timestamp);
        } else {
          return new DateValue($this->getTimestamp() + $timestamp);
        } // if
      } // if
    } // advance
    
    /**
     * Returns true if this date is in range of given dates
     *
     * @param DateValue $from
     * @param DateValue $to
     * @return boolean
     */
    function inRange(DateValue $from, DateValue $to) {
      return ($this->getTimestamp() >= $from->getTimestamp()) && ($this->getTimestamp() <= $to->getTimestamp());
    } // inRange

    /**
     * Returns true if $value falls on the same day as this day
     *
     * @param DateValue $value
     * @return bool
     */
    function isSameDay(DateValue $value) {
      return ($value->getDay() == $this->getDay()) && ($value->getMonth() == $this->getMonth()) && ($value->getYear() == $this->getYear());
    } // isSameDay

    /**
     * This function will return true if this day is today
     *
     * @param integer $offset
     * @return boolean
     */
    function isToday($offset = null) {
      $today = new DateTimeValue(time() + $offset);
      $today->beginningOfDay();

      return $this->getDay()   == $today->getDay() &&
             $this->getMonth() == $today->getMonth() &&
             $this->getYear()  == $today->getYear();
    } // isToday

    /**
     * This function will return true if this date object is yesterday
     *
     * @param integer $offset
     * @return boolean
     */
    function isYesterday($offset = null) {
      return $this->isToday($offset - 86400);
    } // isYesterday

    /**
     * Returns true if this date object is tomorrow
     *
     * @param integer $offset
     * @return boolean
     */
    function isTomorrow($offset = null) {
      return $this->isToday($offset + 86400);
    } // isTomorrow

    /**
     * Is this a weekend day
     *
     * @return boolean
     */
    function isWeekend() {
      return Globalization::isWeekend($this);
    } // isWeekend
    
    /**
     * Returns true if this date is workday
     *
     * @return boolean
     */
    function isWorkday() {
      return Globalization::isWorkday($this);
    } // isWorkday

    /**
     * Returns true if this date is holiday
     *
     * @return boolean
     */
    function isDayOff() {
      return Globalization::isDayOff($this);
    } // isWorkday

    /**
     * Returns if year is leap year
     *
     * @return bool
     */
    function isLeapYear() {
      return ($this->getYear() % 4) === 0;
    } // isLeapYear

    /**
     * This function will move internal data to the beginning of day and return
     * modified object
     *
     * @return DateTimeValue
     */
    function beginningOfDay() {
      return new DateTimeValue(mktime(0, 0, 0, $this->getMonth(), $this->getDay(), $this->getYear()));
    } // beginningOfDay

    /**
     * This function will set hours, minutes and seconds to 23:59:59 and return
     * this object.
     *
     * If you wish to get end of this day simply type:
     *
     * @return DateTimeValue
     */
    function endOfDay() {
      return new DateTimeValue(mktime(23, 59, 59, $this->getMonth(), $this->getDay(), $this->getYear()));
    } // endOfDay

    /**
     * Return beginning of week object
     *
     * @param integer $first_week_day
     * @return DateTimeValue
     */
    function beginningOfWeek($first_week_day = 0) {
      $weekday = $this->getWeekday();
      if ($weekday >= $first_week_day) {
      	$days_delta = $weekday - $first_week_day;
      } else {
      	$days_delta = $weekday - $first_week_day + 7;
      } // if
      
      return $this->beginningOfDay()->advance($days_delta * -86400, false);
    } // beginningOfWeek

    /**
     * Return end of week date time object
     *
     * @param integer $first_week_day
     * @return DateTimeValue
     */
    function endOfWeek($first_week_day = 0) {
    	return $this->beginningOfWeek($first_week_day)->advance(604799, false);
    } // endOfWeek

    /**
     * Calculate difference in days between this day and $second date
     *
     * @param DateValue $second
     * @return integer
     * @throws InvalidParamError
     */
    function daysBetween(DateValue $second) {
      if($second instanceof DateValue) {
        $first_timestamp = mktime(12, 0, 0, $this->getMonth(), $this->getDay(), $this->getYear());
        $second_timestamp = mktime(12, 0, 0, $second->getMonth(), $second->getDay(), $second->getYear());

        if($first_timestamp == $second_timestamp) {
          return 0;
        } // if

        $diff = (integer) abs($first_timestamp - $second_timestamp);
        if($diff < 86400) {
          return $this->getDay() != $second->getDay() ? 1 : 0;
        } else {
          return (integer) round($diff / 86400);
        } // if
      } else {
        throw new InvalidParamError('second', $second, '$second is expected to be instance of DateValue class');
      } // if
    } // daysBetween

    /**
     * Return valid DateValue offset by given user's time zone
     *
     * @param IUser $user
     * @return DateValue
     */
    function getForUser($user = null) {
      if($user instanceof IUser) {
        return new DateValue($this->getTimestamp() + get_user_gmt_offset($user));
      } else {
        return clone($this);
      } // if
    } // getForUser
    
    
    /**
     * Return valid DateValue offset by given user's time zone in GMT
     *
     * @param IUser $user
     * @return DateValue
     */
    function getForUserInGMT($user = null) {
      if($user instanceof IUser) {
        return new DateValue($this->getTimestamp() - get_user_gmt_offset($user));
      } else {
        return clone($this);
      } // if
    } // getForUser

    // ---------------------------------------------------
    //  Format to some standard values
    // ---------------------------------------------------

    /**
     * Return formated datetime
     *
     * @param string $format
     * @return string
     */
    function format($format) {
      return date($format, $this->getTimestamp());
    } // format
    
    /**
     * Format value for given user
     *
     * @param IUser|null $user
     * @param integer $offset
     * @return string
     */
    function formatForUser($user = null, $offset = null) {
      $user = $user instanceof IUser ? $user : Authentication::getLoggedUser();
      
      if($user instanceof IUser) {
        $format = $user->getDateFormat();
      } else {
        $format = FORMAT_DATE;
      } // if
      
      return $this->__formatForUser($format, ($offset === null ? get_user_gmt_offset($user) : (integer) $offset));
    } // formatForUser

    /**
     * Format Date For user
     *
     * @param IUser|null $user
     * @param integer $offset
     * @return string
     */
    function formatDateForUser($user = null, $offset = null) {
      return $this->formatForUser($user, $offset);
    } // formatDateForUser
    
    /**
     * Do actual formatting for given user
     *
     * @param string $format
     * @param integer $offset
     * @return string
     */
    protected function __formatForUser($format, $offset) {
      if(DIRECTORY_SEPARATOR == "\\") {
        $format = str_replace('%e', '%d', $format);
      } // if
      
      return strftime($format, $this->getTimestamp() + $offset);
    } // __formatForUser

    /**
     * Return datetime formated in MySQL datetime format
     *
     * @return string
     */
    function toMySQL() {
      return $this->format(DATE_MYSQL);
    } // toMySQL

    /**
     * Return ISO8601 formated time
     *
     * @return string
     */
    function toISO8601() {
      return $this->format(DATE_ISO8601);
    } // toISO

    /**
     * Return atom formated time (W3C format)
     *
     * @return string
     */
    function toAtom() {
      return $this->format(DATE_ATOM);
    } // toAtom

    /**
     * Return RSS format
     *
     * @return string
     */
    function toRSS() {
      return $this->format(DATE_RSS);
    } // toRSS

    /**
     * Return iCalendar formated date and time
     *
     * @return string
     */
    function toICalendar() {
      return $this->format('Ymd\THis\Z');
    } // toICalendar
    
    // ---------------------------------------------------
    //  Interface implementation
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
      $result = array(
        'class' => 'DateValue', 
        'timestamp' => $this->getTimestamp(), 
        'mysql' => $this->toMySQL(), 
        'formatted' => $this->formatForUser($user), 
        'formatted_gmt' => $this->formatForUser($user, 0),
      	'formatted_time' => '00:00', 
      	'formatted_time_gmt' => '00:00', 
      );
      
      $result['formatted_date'] = $result['formatted']; 
      $result['formatted_date_gmt'] = $result['formatted_gmt'];  
      
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
    } // describe

    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------

    /**
     * Break timestamp into its parts and set internal variables
     */
    function parse() {
      $this->date_data = getdate($this->timestamp);

      if($this->date_data) {
        $this->year = (integer) $this->date_data['year'];
        $this->month = (integer) $this->date_data['mon'];
        $this->day = (integer) $this->date_data['mday'];
      } // if
    } // parse

    /**
     * Update internal timestamp based on internal param values
     */
    function setTimestampFromAttributes() {
      $this->setTimestamp(mktime(0, 0, 0, $this->month, $this->day, $this->year));
    } // setTimestampFromAttributes

    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------

    /**
     * Get timestamp
     *
     * @return integer
     */
    function getTimestamp() {
      return $this->timestamp;
    } // getTimestamp

    /**
     * Set timestamp value
     *
     * @param integer $value
     */
    function setTimestamp($value) {
      $this->timestamp = $value;
      $this->parse();
    } // setTimestamp

    /**
     * Return year
     *
     * @return integer
     */
    function getYear() {
      return $this->year;
    } // getYear

    /**
     * Set year value
     *
     * @param integer $value
     */
    function setYear($value) {
      $this->year = (integer) $value;
      $this->setTimestampFromAttributes();
    } // setYear

    /**
     * Return numberic representation of month
     *
     * @return integer
     */
    function getMonth() {
      return $this->month;
    } // getMonth

    /**
     * Set month value
     *
     * @param integer $value
     */
    function setMonth($value) {
      $this->month = (integer) $value;
      $this->setTimestampFromAttributes();
    } // setMonth

    /**
     * Return days
     *
     * @return integer
     */
    function getDay() {
      return $this->day;
    } // getDay

    /**
     * Set day value
     *
     * @param integer $value
     */
    function setDay($value) {
      $this->day = (integer) $value;
      $this->setTimestampFromAttributes();
    } // setDay

    /**
     * Return weeekday for given date
     *
     * @return integer
     */
    function getWeekday() {
      return isset($this->date_data['wday']) ? $this->date_data['wday'] : null;
    } // getWeekday

    /**
     * Return yearday from given date
     *
     * @return integer
     */
    function getYearday() {
      return isset($this->date_data['yday']) ? $this->date_data['yday'] : null;
    } // getYearday

    /**
     * Return year week
     *
     * @return integer
     */
    function getWeek() {
      return (integer) date('W', $this->getTimestamp());
    } // getWeek

    /**
     * Return ISO value
     *
     * @return string
     */
    function __toString() {
      return $this->toMySQL();
    } // __toString

  }