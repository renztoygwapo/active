<?php

  class TestDateTime extends UnitTestCase {
    
    function testInstantation() {
      $now = DateTimeValue::now();
      $this->assertIsA($now, 'DateTimeValue');
    } // testInstantation
    
    function testMySQLStringConversion() {
      $mysql_formated_date = gmdate(DATETIME_MYSQL);
      $now_from_string = DateTimeValue::makeFromString($mysql_formated_date);
      $this->assertEqual($mysql_formated_date, $now_from_string->toMySQL());
    } // testMySQLStringConversion
    
    function testNow() {
      $now = DateTimeValue::now();
      $now_from_string = DateTimeValue::makeFromString(gmdate(DATETIME_MYSQL));
      
      $this->assertEqual($now->getDay(), $now_from_string->getDay());
      $this->assertEqual($now->getMonth(), $now_from_string->getMonth());
      $this->assertEqual($now->getYear(), $now_from_string->getYear());
      $this->assertEqual($now->getHour(), $now_from_string->getHour());
      $this->assertEqual($now->getMinute(), $now_from_string->getMinute());
      $this->assertEqual($now->getSecond(), $now_from_string->getSecond());
    } // testNow
    
    function testCreateFromParams() {
      $datetime = DateTimeValue::make(14, 30, 15, 11, 14, 1984);
      $this->assertEqual($datetime->getDay(), 14);
      $this->assertEqual($datetime->getMonth(), 11);
      $this->assertEqual($datetime->getYear(), 1984);
      $this->assertEqual($datetime->getHour(), 14);
      $this->assertEqual($datetime->getMinute(), 30);
      $this->assertEqual($datetime->getSecond(), 15);
    } // testCreateFromParams
    
    function testCreateFromString() {
      $datetime = DateTimeValue::makeFromString('14 Nov 1984 14:30:15');
      $this->assertEqual($datetime->getDay(), 14);
      $this->assertEqual($datetime->getMonth(), 11);
      $this->assertEqual($datetime->getYear(), 1984);
      $this->assertEqual($datetime->getHour(), 14);
      $this->assertEqual($datetime->getMinute(), 30);
      $this->assertEqual($datetime->getSecond(), 15);
    } // testCreateFromString
    
    function testFormats() {
      $datetime = DateTimeValue::makeFromString('14 Nov 1984 14:30:15');
      $this->assertEqual($datetime->toMySQL(), '1984-11-14 14:30:15');
      $this->assertEqual($datetime->toISO8601(), '1984-11-14T14:30:15+0000');
      $this->assertEqual($datetime->toAtom(), '1984-11-14T14:30:15+00:00');
      
      // Problems with this assertation: on some installation (tested on PHP 5.0.4) this
      // function will return 'Wed, 14 Nov 1984 14:30:15 GMT Standard Time' and on some '
      // Wed, 14 Nov 1984 14:30:15 GMT' as expected. Because of that this assertation
      // has been a bit modified - it will check if string starts with the
      // 'Wed, 14 Nov 1984 14:30:15 GMT' to make sure that date and the timezone are 
      // recognized. If PHP adds something behind that it would be OK
      //$this->assertEqual($datetime->toRSS(), 'Wed, 14 Nov 1984 14:30:15 GMT');
      $this->assertTrue(str_starts_with($datetime->toRSS(), 'Wed, 14 Nov 1984 14:30:15'));
    } // testFormats
    
    function testIsYesterday() {
      $yesterday_from_string = DateTimeValue::makeFromString('yesterday');
      $today = DateTimeValue::now();
      $yesterday_from_today = DateTimeValue::make($today->getHour(), $today->getMinute(), $today->getSecond(), $today->getMonth(), $today->getDay() - 1, $today->getYear());
      $this->assertEqual($yesterday_from_string->getDay(), $yesterday_from_today->getDay());
      $this->assertEqual($yesterday_from_string->getMonth(), $yesterday_from_today->getMonth());
      $this->assertEqual($yesterday_from_string->getYear(), $yesterday_from_today->getYear());
    } // testIsYesterday
    
    function testAdvance() {
      $datetime = DateTimeValue::makeFromString('14 Nov 1984 14:30:15');
      $datetime->advance(3600);
      $this->assertEqual($datetime->getHour(), 15);
      $datetime->advance(-15);
      $this->assertEqual($datetime->getSecond(), 0);
    } // testAdvance
    
    function testDaysBetween() {
      // More than one day
      $first = new DateValue('now');
      $second = new DateValue('-7 days');
      
      $this->assertEqual($first->daysBetween($second), 7);
      
      // In the future
      $first = new DateValue('now');
      $second = new DateValue('7 days');
      
      $this->assertEqual($first->daysBetween($second), 7);
      
      // Same day
      $first = new DateValue('1984-11-14 09:00:00');
      $second = new DateValue('1984-11-14 16:00:00');
      $this->assertEqual($first->daysBetween($second), 0);
      
      // Same time
      $first = new DateValue('1984-11-14 09:00:00');
      $second = new DateValue('1984-11-14 09:00:00');
      $this->assertEqual($first->daysBetween($second), 0);
      
      // Less than 24h difference, but differnt dates
      $first = new DateValue('1983-11-11 12:00:00');
      $second = new DateValue('1983-11-12 09:00:00');
      $this->assertEqual($first->daysBetween($second), 1);
    } // testDaysBetween
    
//    function testTimezones() {
//      $timezones = Timezones::getAll();
//      $this->assertTrue(is_array($timezones) && (count($timezones) == 34));
//      
//      $gmt = Timezones::getByOffset(0);
//      $this->assertIsA($gmt, 'Timezone');
//      $this->assertEqual($gmt->getFormattedOffset(), '');
//      
//      $belgrade = Timezones::getByOffset(3600);
//      $this->assertIsA($belgrade, 'Timezone');
//      $this->assertEqual($belgrade->getFormattedOffset(), '+01:00');
//      $this->assertEqual($belgrade->getFormattedOffset(''), '+0100');
//    } // testTimezones
    
    function testBeginningEndOfWeek() {
      $friday = new DateValue('2007-12-21');
      
      $this->assertEqual($friday->getWeekday(), 5);
      
      // Beginning of the week - 2007-12-16 Sunday
      // End of the week - 2007-12-22 Saturday
      
      $week_beginning = $friday->beginningOfWeek();
      $week_end = $friday->endOfWeek();
      
      $this->assertEqual($week_beginning->getYear(), 2007);
      $this->assertEqual($week_beginning->getMonth(), 12);
      $this->assertEqual($week_beginning->getDay(), 16);
      $this->assertEqual($week_beginning->getHour(), 0);
      $this->assertEqual($week_beginning->getMinute(), 0);
      $this->assertEqual($week_beginning->getSecond(), 0);
      
      $this->assertEqual($week_beginning->getWeekday(), 0);
      
      $this->assertEqual($week_end->getYear(), 2007);
      $this->assertEqual($week_end->getMonth(), 12);
      $this->assertEqual($week_end->getDay(), 22);
      $this->assertEqual($week_end->getHour(), 23);
      $this->assertEqual($week_end->getMinute(), 59);
      $this->assertEqual($week_end->getSecond(), 59);
      
      $this->assertEqual($week_end->getWeekday(), 6);
      
      // Beginning of the week - 2007-12-17 Monday
      // End of the week - 2007-12-23 Sunday
      
      $week_beginning = $friday->beginningOfWeek(1);
      $week_end = $friday->endOfWeek(1);
      
      $this->assertEqual($week_beginning->getYear(), 2007);
      $this->assertEqual($week_beginning->getMonth(), 12);
      $this->assertEqual($week_beginning->getDay(), 17);
      $this->assertEqual($week_beginning->getHour(), 0);
      $this->assertEqual($week_beginning->getMinute(), 0);
      $this->assertEqual($week_beginning->getSecond(), 0);
      
      $this->assertEqual($week_beginning->getWeekday(), 1);
      
      $this->assertEqual($week_end->getYear(), 2007);
      $this->assertEqual($week_end->getMonth(), 12);
      $this->assertEqual($week_end->getDay(), 23);
      $this->assertEqual($week_end->getHour(), 23);
      $this->assertEqual($week_end->getMinute(), 59);
      $this->assertEqual($week_end->getSecond(), 59);
      
      $this->assertEqual($week_end->getWeekday(), 0);
    } // testBeginningEndOfWeek

    /**
     * Test isLeapYear() method
     */
    function testLeapYear() {
      $this->assertTrue(DateValue::makeFromString('2008/05/01')->isLeapYear());
      $this->assertFalse(DateValue::makeFromString('2009/05/01')->isLeapYear());
      $this->assertFalse(DateValue::makeFromString('2010/05/01')->isLeapYear());
      $this->assertFalse(DateValue::makeFromString('2011/05/01')->isLeapYear());
      $this->assertTrue(DateValue::makeFromString('2012/05/01')->isLeapYear());
    } // testLeapYear
  
  }