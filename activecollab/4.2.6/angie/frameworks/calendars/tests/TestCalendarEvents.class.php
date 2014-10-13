<?php

  /**
   * Test calendar events
   */
  class TestCalendarEvents extends AngieModelTestCase {

    /**
     * Logged user
     *
     * @var User
     */
    private $logged_user;

    /**
     * Calendar instance used for testing
     *
     * @var Calendar
     */
    private $calendar;

    /**
     * Set up test instances
     */
    function setUp() {
      parent::setUp();

      $this->logged_user = Users::findById(1);

      $this->calendar = new UserCalendar();
      $this->calendar->setName('Test Calendar');
      $this->calendar->setState(STATE_VISIBLE);
      $this->calendar->save();
    } // setUp

    /**
     * Tear down test instances
     */
    function tearDown() {
      $this->logged_user = null;
      $this->calendar = null;

      parent::tearDown();
    } // tearDown

    /**
     * Test setup
     */
    function testInitialization() {
      $this->assertTrue($this->logged_user->isLoaded(), 'User is loaded');
      $this->assertTrue($this->logged_user->isAdministrator(), 'Logged user is administrator');

      $this->assertTrue($this->calendar->isLoaded(), 'Calendar is loaded');
      $this->assertEqual($this->calendar->getState(), STATE_VISIBLE, 'Calendar has a valid state');
    } // testInitialization

    /**
     * Test CalendarEvents::dateOrRangeToRange() implementation
     */
    function testDateOrRangeConversion() {
      list($from_date, $to_date) = CalendarEvents::dateOrRangeToRange(new DateValue('2012/07/31'));

      $this->assertIsA($from_date, 'DateValue', 'Valid $from_date instance');
      $this->assertEqual($from_date->toMySQL(), '2012-07-31', 'Valid $from_date value');
      $this->assertIsA($to_date, 'DateValue', 'Valid $to_date instance');
      $this->assertEqual($to_date->toMySQL(), '2012-07-31', 'Valid $to_date value');

      list($from_date, $to_date) = CalendarEvents::dateOrRangeToRange('2012/06/05');

      $this->assertIsA($from_date, 'DateValue', 'Valid $from_date instance');
      $this->assertEqual($from_date->toMySQL(), '2012-06-05', 'Valid $from_date value');
      $this->assertIsA($to_date, 'DateValue', 'Valid $to_date instance');
      $this->assertEqual($to_date->toMySQL(), '2012-06-05', 'Valid $to_date value');

      list($from_date, $to_date) = CalendarEvents::dateOrRangeToRange(strtotime('2007/10/05'));

      $this->assertIsA($from_date, 'DateValue', 'Valid $from_date instance');
      $this->assertEqual($from_date->toMySQL(), '2007-10-05', 'Valid $from_date value');
      $this->assertIsA($to_date, 'DateValue', 'Valid $to_date instance');
      $this->assertEqual($to_date->toMySQL(), '2007-10-05', 'Valid $to_date value');

      // Two DateValue instances
      list($from_date, $to_date) = CalendarEvents::dateOrRangeToRange(array(new DateValue('2007/03/25'), new DateValue('2007/10/05')));

      $this->assertIsA($from_date, 'DateValue', 'Valid $from_date instance');
      $this->assertEqual($from_date->toMySQL(), '2007-03-25', 'Valid $from_date value');
      $this->assertIsA($to_date, 'DateValue', 'Valid $to_date instance');
      $this->assertEqual($to_date->toMySQL(), '2007-10-05', 'Valid $to_date value');

      // Two string representations of a date
      list($from_date, $to_date) = CalendarEvents::dateOrRangeToRange(array('2007/03/25', '2007/10/05'));

      $this->assertIsA($from_date, 'DateValue', 'Valid $from_date instance');
      $this->assertEqual($from_date->toMySQL(), '2007-03-25', 'Valid $from_date value');
      $this->assertIsA($to_date, 'DateValue', 'Valid $to_date instance');
      $this->assertEqual($to_date->toMySQL(), '2007-10-05', 'Valid $to_date value');

      // Two timestamps
      list($from_date, $to_date) = CalendarEvents::dateOrRangeToRange(array(strtotime('2007/03/25'), strtotime('2007/10/05')));

      $this->assertIsA($from_date, 'DateValue', 'Valid $from_date instance');
      $this->assertEqual($from_date->toMySQL(), '2007-03-25', 'Valid $from_date value');
      $this->assertIsA($to_date, 'DateValue', 'Valid $to_date instance');
      $this->assertEqual($to_date->toMySQL(), '2007-10-05', 'Valid $to_date value');
    } // testDateOrRangeConversion

    /**
     * Test event creation
     */
    function testEventCreation() {
      $version_one_development = $this->calendar->calendarEvents()->add('activeCollab 1.0 Development', array(
        DateValue::makeFromString('2007/03/25'),
        DateValue::makeFromString('2007/10/05')
      ), true);

      $this->assertEqual($version_one_development->getParentType(), 'UserCalendar', 'Parent is properly set');
      $this->assertEqual($version_one_development->getParentId(), 1, 'Parent is properly set');

      $version_one_launch = $this->calendar->calendarEvents()->add("activeCollab 1.0 Launch", DateValue::makeFromString('2007/10/05'), true);
      $version_two_launch = $this->calendar->calendarEvents()->add("activeCollab 2.0 Launch", DateValue::makeFromString('2009/04/28'), true);
      $version_three_launch = $this->calendar->calendarEvents()->add("activeCollab 3.0 Launch", DateValue::makeFromString('2012/05/21'), true);
      
      $this->assertTrue($version_one_development->isLoaded(), 'Event #1 created');
      $this->assertTrue($version_one_launch->isLoaded(), 'Event #2 created');
      $this->assertTrue($version_two_launch->isLoaded(), 'Event #3 created');
      $this->assertTrue($version_three_launch->isLoaded(), 'Event #4 created');
      
      $this->assertTrue($version_one_development->isSpan());
      $this->assertFalse($version_one_launch->isSpan());
      
      $this->assertEqual($this->calendar->calendarEvents()->count($this->logged_user), 4, 'Four events in the calendar');
      
      // Test span!
      $this->assertEqual($this->calendar->calendarEvents()->countFor('2007/03/08', $this->logged_user), 0, 'No events of March 3rd 2007');
      $this->assertEqual($this->calendar->calendarEvents()->countFor('2007/03/25', $this->logged_user), 1, 'activeCollab 1.0 development started on this date');
      $this->assertEqual($this->calendar->calendarEvents()->countFor('2007/07/15', $this->logged_user), 1, 'In the middle of activeCollab 1.0 development');
      $this->assertEqual($this->calendar->calendarEvents()->countFor('2007/10/05', $this->logged_user), 2, 'End of activeCollab 1.0 development and launch');
    } // testEventCreation

    /**
     * Test daily reocurring events
     */
    function testDailyReoccurringEvents() {
      $current_year = (integer) date('Y');

      $test_event = $this->calendar->calendarEvents()->add('Test Event', "{$current_year}/11/11");
      $test_event->setRepeatEvent(CalendarEvent::REPEAT_DAILY);
      $test_event->save();

      $this->assertTrue($test_event->isLoaded(), 'Event has been saved');
      $this->assertTrue($test_event->isRepeating(), 'Repeting event');

      $this->assertEqual($this->calendar->calendarEvents()->countFor('1983/11/11', $this->logged_user), 0, "Doesn't repeat in the past");
      $this->assertEqual($this->calendar->calendarEvents()->countFor("{$current_year}/11/10", $this->logged_user), 0, 'Does not repeat in the past');
      $this->assertEqual($this->calendar->calendarEvents()->countFor("{$current_year}/11/11", $this->logged_user), 1, 'Start repeting from the day it was defined');
      $this->assertEqual($this->calendar->calendarEvents()->countFor("{$current_year}/11/12", $this->logged_user), 1, 'Repeat the day after');
      $this->assertEqual($this->calendar->calendarEvents()->countFor('2052/02/03', $this->logged_user), 1, 'Repeat in the distant future');

      $this->assertEqual($this->calendar->calendarEvents()->countFor(array('1983/11/10', '1983/11/12'), $this->logged_user), 0, "Doesn't repeat in the past");
      $this->assertEqual($this->calendar->calendarEvents()->countFor(array("{$current_year}/11/10", "{$current_year}/11/12"), $this->logged_user), 1, 'Start repeting from the day it was defined (matched by an exact match)');
      $this->assertEqual($this->calendar->calendarEvents()->countFor(array('2052/02/03', '2052/02/08'), $this->logged_user), 1, 'Repeat in the distant future (matched by repeat behavior)');
    } // testDailyReoccurringEvents

    /**
     * Test weekly reocurring events
     */
    function testWeeklyReoccurringEvents() {
      $repeat_each_monday = $this->calendar->calendarEvents()->add('Monday', "2012/07/09");
      $repeat_each_monday->setRepeatEvent(CalendarEvent::REPEAT_WEEKLY);
      $repeat_each_monday->save();

      $this->assertTrue($repeat_each_monday->isLoaded(), 'Event has been saved');
      $this->assertTrue($repeat_each_monday->getStartsOn()->getWeekday(), 1, 'Monday saved');
      $this->assertTrue($repeat_each_monday->isRepeating(), 'Repeting event');

      $repeat_each_friday = $this->calendar->calendarEvents()->add('Friday', "2012/07/20");
      $repeat_each_friday->setRepeatEvent(CalendarEvent::REPEAT_WEEKLY);
      $repeat_each_friday->save();

      $this->assertTrue($repeat_each_friday->isLoaded(), 'Event has been saved');
      $this->assertTrue($repeat_each_monday->getStartsOn()->getWeekday(), 5, 'Friday saved');
      $this->assertTrue($repeat_each_friday->isRepeating(), 'Repeting event');

      $repeat_each_wednesday = $this->calendar->calendarEvents()->add('Wednesday', "2012/07/25");
      $repeat_each_wednesday->setRepeatEvent(CalendarEvent::REPEAT_WEEKLY);
      $repeat_each_wednesday->save();

      $this->assertTrue($repeat_each_wednesday->isLoaded(), 'Event has been saved');
      $this->assertTrue($repeat_each_monday->getStartsOn()->getWeekday(), 3, 'Wednesday saved');
      $this->assertTrue($repeat_each_wednesday->isRepeating(), 'Repeting event');

      $this->assertEqual($this->calendar->calendarEvents()->countFor(array('2012/07/09', '2012/07/11'), $this->logged_user), 1, "Match Monday, but not Wednesday (it's two weeks later)");
      $this->assertEqual($this->calendar->calendarEvents()->countFor(array('2012/07/16', '2012/07/20'), $this->logged_user), 2, "Monday repeated in this week, Friday defined in this week, but no Wednesday occurence (it's one week later)");
      $this->assertEqual($this->calendar->calendarEvents()->countFor(array('2012/07/23', '2012/07/27'), $this->logged_user), 3, "Monday repeated in this week, Friday as well and first Wednesday occurence");
      $this->assertEqual($this->calendar->calendarEvents()->countFor(array('2012/07/30', '2012/08/03'), $this->logged_user), 3, "Monday, Friday and Wednesday, repeated");
    } // testWeeklyReoccurringEvents

    /**
     * Test montly reocurring events
     */
    function testMonthlyReoccurringEvents() {
      $current_year = (integer) date('Y');

      $first = $this->calendar->calendarEvents()->add('First Month Day', "{$current_year}/05/01");
      $first->setRepeatEvent(CalendarEvent::REPEAT_MONTHLY);
      $first->save();

      $this->assertTrue($first->isLoaded(), 'Event has been saved');
      $this->assertTrue($first->isRepeating(), 'Repeting event');

      $this->assertEqual($this->calendar->calendarEvents()->countFor('1982/05/01', $this->logged_user), 0, "Doesn't repeat in the past");
      $this->assertEqual($this->calendar->calendarEvents()->countFor("{$current_year}/05/01", $this->logged_user), 1, 'First event occurrance');
      $this->assertEqual($this->calendar->calendarEvents()->countFor('2052/05/01', $this->logged_user), 1, 'Repeats in the future');

      $this->assertEqual($this->calendar->calendarEvents()->countFor(array('1982/05/01', '1982/06/01'), $this->logged_user), 0, "Doesn't repeat in the past, even for range");
      $this->assertEqual($this->calendar->calendarEvents()->countFor(array("{$current_year}/04/01", "{$current_year}/05/31"), $this->logged_user), 1, 'First event occurrance, range');
      $this->assertEqual($this->calendar->calendarEvents()->countFor(array("2052/04/01", "2052/05/31"), $this->logged_user), 1, 'Event in the future, range match');
      $this->assertEqual($this->calendar->calendarEvents()->countFor(array("2052/04/30", "2052/05/02"), $this->logged_user), 1, 'Event in the future, range match that does not match entire month, but specific month days');

      $last = $this->calendar->calendarEvents()->add('Last Day in the Month', "{$current_year}/06/30");
      $last->setRepeatEvent(CalendarEvent::REPEAT_MONTHLY);
      $last->save();

      $this->assertTrue($last->isLoaded(), 'Event has been saved');
      $this->assertTrue($last->isRepeating(), 'Repeting event');

      $this->assertEqual($this->calendar->calendarEvents()->countFor('1982/06/30', $this->logged_user), 0, "Doesn't repeat in the past");
      $this->assertEqual($this->calendar->calendarEvents()->countFor("{$current_year}/06/30", $this->logged_user), 1, 'First event occurrance');
      $this->assertEqual($this->calendar->calendarEvents()->countFor('2052/07/30', $this->logged_user), 1, 'Repeats in the future');
      $this->assertEqual($this->calendar->calendarEvents()->countFor('2052/02/28', $this->logged_user), 0, "Doesn't match february");
    } // testMonthlyReoccurringEvents

    /**
     * Test yearly reocurring events
     */
    function testYearlyReoccurringEvents() {
      $current_year = (integer) date('Y');
      
      $may_the_first = $this->calendar->calendarEvents()->add('May 1st', "{$current_year}/05/01");
      $may_the_first->setRepeatEvent(CalendarEvent::REPEAT_YEARLY);
      $may_the_first->save();
      
      $this->assertTrue($may_the_first->isLoaded(), 'Event has been saved');
      $this->assertTrue($may_the_first->isRepeating(), 'Repeting event');

      // Test past, present, future
      $this->assertEqual($this->calendar->calendarEvents()->countFor('1982/05/01', $this->logged_user), 0, "Doesn't repeat in the past");
      $this->assertEqual($this->calendar->calendarEvents()->countFor("{$current_year}/05/01", $this->logged_user), 1, 'This year May the first');
      $this->assertEqual($this->calendar->calendarEvents()->countFor('2052/05/01', $this->logged_user), 1, 'Future May the first');

      // Test match in range
      $this->assertEqual($this->calendar->calendarEvents()->countFor(array("1982/04/01", "1982/06/01"), $this->logged_user), 0, 'Past ocurrence should not exist'); // Future occurance
      $this->assertEqual($this->calendar->calendarEvents()->countFor(array("{$current_year}/04/01", "{$current_year}/06/01"), $this->logged_user), 1, 'Exact match in range (first occurence)'); // Current year, for exact match
      $this->assertEqual($this->calendar->calendarEvents()->countFor(array("2052/04/01", "2052/06/01"), $this->logged_user), 1, 'There should be one future ocurrence'); // Future occurance
    } // testYearlyReoccurringEvents

    /**
     * Test CalendarEvents::matchWholeYear() utility method
     */
    function testMatchWholeYear() {
      $this->assertFalse(CalendarEvents::matchWholeYear(new DateValue('2013/05/08'), new DateValue('2011/12/05')), '$from should not be larger than $to');

      $this->assertFalse(CalendarEvents::matchWholeYear(new DateValue('2012/05/08'), new DateValue('2012/12/05')), 'Less than a year, fall in the same year');

      $this->assertTrue(CalendarEvents::matchWholeYear(new DateValue('2010/05/08'), new DateValue('2011/05/07')), 'A year, because both days (2010/05/08-2011/05/07) are included');
      $this->assertTrue(CalendarEvents::matchWholeYear(new DateValue('2010/05/08'), new DateValue('2011/05/08')), 'Same dates from two different years, make a year');

      $this->assertFalse(CalendarEvents::matchWholeYear(new DateValue('2012/01/04'), new DateValue('2013/01/02')), 'Not a whole year, one day missing (but we fetch leap year)');
      $this->assertFalse(CalendarEvents::matchWholeYear(new DateValue('2011/05/08'), new DateValue('2012/05/06')), 'Not a whole year, one day missing (but we fetch leap year)');

      $this->assertFalse(CalendarEvents::matchWholeYear(new DateValue('2012/01/01'), new DateValue('2012/12/30')), 'Leap year problem when 365 days is used for calculation');
      $this->assertFalse(CalendarEvents::matchWholeYear(new DateValue('2012/01/02'), new DateValue('2012/12/31')), 'Leap year problem when 365 days is used for calculation');

      $this->assertTrue(CalendarEvents::matchWholeYear(new DateValue('2012/01/01'), new DateValue('2012/12/31')), 'Whole (leap) year');
    } // testYearOrMore

    /**
     * Test match weekday result
     */
    function testMatchWeekday() {
      $this->assertEqual(CalendarEvents::matchWeekdays(new DateValue('2012/07/02'), new DateValue('2012/07/08')), 'any', 'Full week');
      $this->assertEqual(CalendarEvents::matchWeekdays(new DateValue('2012/07/02'), new DateValue('2012/07/02')), array(1), 'One day');
      $this->assertEqual(CalendarEvents::matchWeekdays(new DateValue('2012/07/02'), new DateValue('2012/07/06')), array(1, 2, 3, 4, 5), 'Monday to Friday');
    } // testMatchWeekday

    /**
     * Test match year-month-day filter preparation
     */
    function testMachYearMonthAndDay() {
      $this->assertEqual(CalendarEvents::matchYearMonthAndDay(new DateValue('2000/02/01'), new DateValue('2000/02/29')), array(
        '2000' => array(
          2 => 'any'
        ),
      ), 'Whole month');

      $this->assertEqual(CalendarEvents::matchYearMonthAndDay(new DateValue('2001/01/13'), new DateValue('2001/01/18')), array(
        '2001' => array(
          1 => array(13, 14, 15, 16, 17, 18)
        ),
      ), 'In month');

      $this->assertEqual(CalendarEvents::matchYearMonthAndDay(new DateValue('2001/02/25'), new DateValue('2001/03/05')), array(
        '2001' => array(
          2 => array(25, 26, 27, 28),
          3 => array(1, 2, 3, 4, 5)
        ),
      ), 'Between two neighbour months');

      $this->assertEqual(CalendarEvents::matchYearMonthAndDay(new DateValue('2001/02/25'), new DateValue('2001/06/03')), array(
        '2001' => array(
          2 => array(25, 26, 27, 28),
          3 => 'any',
          4 => 'any',
          5 => 'any',
          6 => array(1, 2, 3)
        ),
      ), 'Between two months that have at least one month in between');

      $this->assertEqual(CalendarEvents::matchYearMonthAndDay(new DateValue('2001/12/25'), new DateValue('2002/01/05')), array(
        '2001' => array(
          12 => array(25, 26, 27, 28, 29, 30, 31),
        ),
        '2002' => array(
          1 => array(1, 2, 3, 4, 5)
        )
      ), 'Different years, neighbour months');

      $this->assertEqual(CalendarEvents::matchYearMonthAndDay(new DateValue('2001/11/25'), new DateValue('2002/03/05')), array(
        '2001' => array(
          11 => array(25, 26, 27, 28, 29, 30),
          12 => 'any'
        ),
        '2002' => array(
          1 => 'any',
          2 => 'any',
          3 => array(1, 2, 3, 4, 5)
        )
      ), 'Different years, months with at least one month in between');

      $this->assertEqual(CalendarEvents::matchYearMonthAndDay(new DateValue('2001/11/01'), new DateValue('2004/02/29')), array(
        '2001' => array(
          11 => 'any',
          12 => 'any'
        ),
        '2002' => 'any',
        '2003' => 'any',
        '2004' => array(
          1 => 'any',
          2 => 'any',
        )
      ), 'Different years, begining of month in first, end of month in last');
    } // testMachYearMonthAndDay
    
  }