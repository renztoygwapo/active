<?php

  /**
   * activeCollab specific globalization behavior
   *
   * @package activeCollab
   * @subpackage resources
   */
  class ActiveCollabGlobalizationAdapter extends GlobalizationAdapter {
    
    /**
     * Loaded configuration value for time_workdays setting
     *
     * @var array
     */
    private $workdays = false;
    
    /**
     * Loaded array of days off definitions
     *
     * @var array
     */
    private $days_off = false;
    
    /**
     * Cached array of values for specific days
     * 
     * Key is date in MySQL format and value is whether day is workday or not
     *
     * @var array
     */
    private $workday_map = array();
    
    /**
     * array of mapped days off with key in format day_month and with values array('is_repeat', 'year');
     * 
     * @var array
     */
    private $days_off_for_js_map = false;
    
    /**
     * Returns true if $date is workday
     *
     * @param DateValue $date
     * @return boolean
     */
    function isWorkday(DateValue $date) {
      if (!($date instanceof DateValue)) {
        return false;
      } // if
      
      if($this->workdays === false) {
        $this->getWorkDays();
      } // if
      
      if($this->days_off === false) {
        $this->getDaysOff();
      } // if
      
      $formatted_date = $date->toMySQL();
      
      if(!array_key_exists($formatted_date, $this->workday_map)) {
        if(in_array($date->getWeekday(), $this->workdays)) {
          $is_day_off = false;
          
          if(is_foreachable($this->days_off)) {
            foreach($this->days_off as $day_off) {
              if($date->getDay() == $day_off['day'] && $date->getMonth() == $day_off['month']) {
                if($day_off['repeat_yearly']) {
                  $is_day_off = true;
                } else {
                  if($date->getYear() == $day_off['year']) {
                    $is_day_off = true;
                  } // if
                } // if
                
                if($is_day_off) {
                  break; // Done here, already know that this is a day off...
                } // if
              } // if
            } // foreach
          } // if
          
          $this->workday_map[$formatted_date] = !$is_day_off; // Workday only if not day off
        } else {
          $this->workday_map[$formatted_date] = false; // We already know that it's a day off
        } // if
      } // if
      
      return $this->workday_map[$formatted_date];
    } // isWorkday
    
    /**
     * get array of work days in a week
     * 
     * @return array
     */
    function getWorkDays() {
      if ($this->workdays === false) {
        $workdays = ConfigOptions::getValue('time_workdays');
        if (!$workdays || !is_foreachable($workdays)) {
          $this->workdays = array(1,2,3,4,5);  
        } else {
          for ($x = 0; $x < count($workdays); $x++) {
            $this->workdays[$x] = (int) $workdays[$x];
          } // if
        } // if
      } // if
      return $this->workdays;
    } // getWorkDays
    
    /**
     * Get array of days off
     * 
     * @return array
     */
    function getDaysOff() {
      if ($this->days_off === false) {
        $this->days_off = AngieApplication::cache()->get('days_off', function() {
          $result = array();

          $days_off = DB::execute("SELECT DAY(event_date) AS 'day', MONTH(event_date) AS 'month', YEAR(event_date) AS 'year', repeat_yearly, name FROM " . TABLE_PREFIX . 'day_offs ORDER BY event_date');
          if ($days_off) {
            foreach ($days_off as $day_off) {
              $result[] = $day_off;
            } // foreach
          } // if

          return $result;
        });
      } // if
      
      return $this->days_off;
    } // getDaysOff
    
    /**
     * Checks if date is a day off
     * 
     * @param DateValue $date
     * @return Boolean
     */
    function isDayOff(DateValue $date) {
      if ($this->days_off === false) {
        $this->days_off = $this->getDaysOff();
      } // if
      
      if (is_foreachable($this->days_off)) {
        foreach($this->days_off as $day_off) {
          if($date->getDay() == $day_off['day'] && $date->getMonth() == $day_off['month']) {
            if($day_off['repeat_yearly']) {
              return true;
            } else {
              return $date->getYear() == $day_off['year'];
            } // if
          } //if
        } //foreach
      } //if
      return false;
    } //isDayOff
    
    
    /**
     * Map for Javascript
     * 
     * @return array
     */
    function getDaysOffMappedForJs() {
      if ($this->days_off_for_js_map === false) {
        if ($this->days_off === false) {
          $this->getDaysOff();
        } // if
        
        if (is_foreachable($this->days_off)) {
          foreach ($this->days_off as $day_off) {
            $this->days_off_for_js_map[$day_off['day'].'/'.$day_off['month']] = array(
              'name'    => $day_off['name'],
              'year'    => $day_off['year'],
              'repeat'  => $day_off['repeat_yearly']    
            );  
          } // foreach
        } // if
      } // if
      return $this->days_off_for_js_map;
    } // getDaysOffMappedForJs
    
  }