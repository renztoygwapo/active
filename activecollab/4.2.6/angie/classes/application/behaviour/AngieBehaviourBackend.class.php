<?php

  /**
   * Angie behavior backend
   *
   * @package angie.library.application
   * @subpackage behaviour
   */
  abstract class AngieBehaviourBackend {

    /**
     * Record an event
     *
     * @param string $event_class
     * @param array $event_tags
     * @param integer $timestamp
     */
    abstract function record($event_class, array $event_tags, $timestamp);

    /**
     * Record fulfilment of an intent
     *
     * @param integer $intent_id
     * @param array $extra_event_tags
     * @param integer $timestamp
     * @param Closure $on_intent_not_found
     */
    abstract function recordFulfilment($intent_id, $extra_event_tags, $timestamp, $on_intent_not_found);

    /**
     * Record an intent and return intent ID
     *
     * @param string $event_class
     * @param array $event_tags
     * @param integer $timestamp
     */
    abstract function recordIntent($event_class, array $event_tags, $timestamp);

  }