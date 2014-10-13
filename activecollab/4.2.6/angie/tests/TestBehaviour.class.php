<?php

  require_once ANGIE_PATH . '/classes/application/behaviour/AngieBehaviourDelegate.class.php';

  /**
   * Class TestBehaviourBackend
   */
  class TestBehaviourBackend extends AngieBehaviourBackend {

    /**
     * List of recorded events
     *
     * @var array
     */
    private $recorded_events = array();

    /**
     * Return recorded events
     *
     * @return array
     */
    function getRecordedEvents() {
      return $this->recorded_events;
    } // getRecordedEvents

    /**
     * Recorded intents
     *
     * @var array
     */
    private $recorded_intents = array();

    /**
     * Record an event
     *
     * @param string $event_class
     * @param array $event_tags
     * @param integer $timestamp
     */
    function record($event_class, array $event_tags, $timestamp) {
      $this->recorded_events[] = array($event_class, $event_tags, null, $timestamp);
    } // record

    /**
     * Record fulfilment of an intent
     *
     * @param integer $intent_id
     * @param array $extra_event_tags
     * @param integer $timestamp
     * @param Closure $on_intent_not_found
     */
    function recordFulfilment($intent_id, $extra_event_tags, $timestamp, $on_intent_not_found) {
      if(isset($this->recorded_intents[$intent_id])) {
        list($event_class, $event_tags, $intent_timestamp) = $this->recorded_intents[$intent_id];

        if(!is_array($extra_event_tags)) {
          $extra_event_tags = $extra_event_tags ? explode(',', $extra_event_tags) : array();
        } // if

        $this->recorded_events[] = array($event_class, array_merge($event_tags, $extra_event_tags), $intent_timestamp, $timestamp);

        unset($this->recorded_intents[$intent_id]);
      } else {
        if($on_intent_not_found instanceof Closure) {
          $event_data = $on_intent_not_found();

          if(is_array($event_data) && count($event_data) == 3) {
            $this->record($event_data[0], AngieApplication::behaviour()->eventTagsToArray($event_data[1]), $event_data[2]);
          } // if
        } // if
      } // if
    } // recordFulfilment

    /**
     * Record an intent and return intent ID
     *
     * @param string $event_class
     * @param array $event_tags
     * @param integer $timestamp
     * @return string
     */
    function recordIntent($event_class, array $event_tags, $timestamp) {
      do {
        $intent_id = make_string();
      } while(isset($this->recorded_intents[$intent_id]));

      $this->recorded_intents[$intent_id] = array($event_class, $event_tags, $timestamp);

      return $intent_id;
    } // recordIntent

    /**
     * Return array of recorded intents
     *
     * @return array
     */
    function getRecordedIntents() {
      return $this->recorded_intents;
    } // getRecordedIntents

  }

  /**
   * Test behavior
   *
   * @package angie.tests
   */
  class TestBehaviour extends UnitTestCase {

    /**
     * Set up environment
     */
    function setUp() {
      parent::setUp();

      AngieApplication::behaviour()->setBackend(new TestBehaviourBackend());
    } // setUp

    /**
     * Test record
     */
    function testRecord() {
      $timestamp = time();

      AngieApplication::behaviour()->record('forgot_password', null, $timestamp);
      AngieApplication::behaviour()->record('forgot_password', 'tag', $timestamp);
      AngieApplication::behaviour()->record('forgot_password', array('tag1', 'tag2'), $timestamp);

      $this->assertEqual(AngieApplication::behaviour()->getBackend()->getRecordedEvents(), array(
        array('forgot_password', array(), null, $timestamp),
        array('forgot_password', array('tag'), null, $timestamp),
        array('forgot_password', array('tag1', 'tag2'), null, $timestamp),
      ));
    } // testRecord

    /**
     * Test intent to fulfilment
     */
    function testIntentAndFulfilment() {
      $timestamp = time();
      $past_timestamp = $timestamp - 120;

      $first_intent_id = AngieApplication::behaviour()->recordIntent('forgot_password', null, $past_timestamp);
      $this->assertNotNull($first_intent_id);

      $second_intent_id = AngieApplication::behaviour()->recordIntent('forgot_password', 'tag', $past_timestamp);
      $this->assertNotNull($second_intent_id);

      $this->assertEqual(AngieApplication::behaviour()->getBackend()->getRecordedIntents(), array(
        $first_intent_id => array('forgot_password', array(), $past_timestamp),
        $second_intent_id => array('forgot_password', array('tag'), $past_timestamp),
      ));

      AngieApplication::behaviour()->recordFulfilment($second_intent_id, 'tag1,tag2', $timestamp);

      $this->assertEqual(AngieApplication::behaviour()->getBackend()->getRecordedEvents(), array(
        array('forgot_password', array('tag', 'tag1', 'tag2'), $past_timestamp, $timestamp),
      ));

      $this->assertEqual(AngieApplication::behaviour()->getBackend()->getRecordedIntents(), array(
        $first_intent_id => array('forgot_password', array(), $past_timestamp),
      ));

      AngieApplication::behaviour()->recordFulfilment($first_intent_id, array('X' , 'Y'), $timestamp);

      $this->assertEqual(AngieApplication::behaviour()->getBackend()->getRecordedEvents(), array(
        array('forgot_password', array('tag', 'tag1', 'tag2'), $past_timestamp, $timestamp),
        array('forgot_password', array('X', 'Y'), $past_timestamp, $timestamp),
      ));

      $this->assertEqual(AngieApplication::behaviour()->getBackend()->getRecordedIntents(), array());
    } // testIntentAndFulfilment

    /**
     * Test missing intent situation
     */
    function testMissingIntent() {
      $timestamp = time();

      $this->assertEqual(AngieApplication::behaviour()->getBackend()->getRecordedEvents(), array());
      $this->assertEqual(AngieApplication::behaviour()->getBackend()->getRecordedIntents(), array());

      AngieApplication::behaviour()->recordFulfilment('12345', null, $timestamp, function() use ($timestamp) {
        return array('forgot_password', 'tag', $timestamp);
      });

      $this->assertEqual(AngieApplication::behaviour()->getBackend()->getRecordedEvents(), array(
        array('forgot_password', array('tag'), null, $timestamp),
      ));
    } // testMissingIntent

  }