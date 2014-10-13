<?php

  // Use required resources
  require_once __DIR__ . '/AngieBehaviourBackend.class.php';

  /**
   * Behavior delegate
   *
   * @package angie.library.application
   * @subpackage behaviour
   */
  class AngieBehaviourDelegate extends AngieDelegate {

    /**
     * Backend instance
     *
     * @var AngieBehaviourBackend
     */
    private $backend;

    /**
     * Return backend instance
     *
     * @return AngieBehaviourBackend
     */
    function &getBackend() {
      return $this->backend;
    } // getBackend

    /**
     * Set bahaviour backend
     *
     * @param AngieBehaviourBackend $backend
     * @throws InvalidInstanceError
     */
    function setBackend($backend) {
      if($backend instanceof AngieBehaviourBackend || $backend === null) {
        $this->backend = $backend;
      } else {
        throw new InvalidInstanceError('backend', $backend, 'AngieBehaviourBackend');
      } // if
    } // setBackend

    /**
     * Return true if behaviour tracking is enabled
     *
     * @return bool
     */
    function isTrackingEnabled() {
      return TRACK_USER_BEHAVIOUR;
    } // isTrackingEnabled

    /**
     * Record an event
     *
     * @param string $event_class
     * @param array $event_tags
     * @param integer $timestamp
     */
    function record($event_class, $event_tags = null, $timestamp = null) {
      if($this->backend instanceof AngieBehaviourBackend) {
        if(empty($timestamp)) {
          $timestamp = time();
        } // if

        $this->backend->record($event_class, $this->eventTagsToArray($event_tags), $timestamp);
      } // if
    } // record

    /**
     * Record fulfilment of an intent
     *
     * @param integer $intent_id
     * @param array|null $extra_event_tags
     * @param integer|null $timestamp
     * @param Closure $on_intent_not_found
     */
    function recordFulfilment($intent_id, $extra_event_tags = null, $timestamp = null, $on_intent_not_found = null) {
      if($this->backend instanceof AngieBehaviourBackend) {
        if(empty($timestamp)) {
          $timestamp = time();
        } // if

        $this->backend->recordFulfilment($intent_id, $extra_event_tags, $timestamp, $on_intent_not_found);
      } // if
    } // recordFulfilment

    /**
     * Record an intent and return intent ID
     *
     * @param string $event_class
     * @param array $event_tags
     * @param integer $timestamp
     * @return mixed
     */
    function recordIntent($event_class, $event_tags = null, $timestamp = null) {
      if($this->backend instanceof AngieBehaviourBackend) {
        if(empty($timestamp)) {
          $timestamp = time();
        } // if

        return $this->backend->recordIntent($event_class, $this->eventTagsToArray($event_tags), $timestamp);
      } else {
        return null;
      } // if
    } // recordIntent

    // ---------------------------------------------------
    //  Tag handling
    // ---------------------------------------------------

    /**
     * List of tag decorators
     *
     * @var array
     */
    private $event_tag_decorators = array();

    /**
     * Add a tag decorator
     *
     * @param callable $decorator
     * @throws InvalidInstanceError
     */
    function addEventTagsDecorator(Closure $decorator) {
      if($decorator instanceof Closure) {
        $this->event_tag_decorators[] = $decorator;
      } else {
        throw new InvalidInstanceError('decorator', $decorator, 'Closure');
      }
    } // addEventTagsDecorator

    /**
     * Event tags to array
     *
     * @param array|string|null $event_tags
     * @return array
     */
    function eventTagsToArray($event_tags) {
      if(!is_array($event_tags)) {
        $event_tags = $event_tags ? explode(',', $event_tags) : array();
      } // if

      foreach($this->event_tag_decorators as $decorator) {
        $decorator($event_tags);
      } // foreach

      return $event_tags;
    } // eventTagsToArray

  }