<?php

  // Use required resources
  require_once ANGIE_PATH . '/classes/application/behaviour/AngieBehaviourBackend.class.php';
  require_once ANGIE_PATH . '/vendor/elastica/init.php';

  /**
   * Elastica powered behaviour backend
   *
   * @package angie.library.application
   * @subpackage behaviour
   */
  class ElasticaBehaviourBackend extends AngieBehaviourBackend {

    /**
     * Record an event
     *
     * @param string $event_class
     * @param array $event_tags
     * @param integer $timestamp
     */
    function record($event_class, array $event_tags, $timestamp) {
      $this->getEventsType()->addDocument(new \Elastica\Document('', array(
        'event_class' => $event_class,
        'event_tags' => $event_tags,
        '@timestamp' => date(DateTime::RFC3339, $timestamp),
      )));
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
      if($intent_id) {
        try {
          $intent = $this->getIntentsType()->getDocument($intent_id);

          $intent_data = $intent->getData();

          if(!is_array($extra_event_tags)) {
            $extra_event_tags = $extra_event_tags ? explode(',', $extra_event_tags) : array();
          } // if

          foreach($extra_event_tags as $tag) {
            if(!in_array($tag, $intent_data['event_tags'])) {
              $intent_data['event_tags'][] = $tag;
            } // if
          } // foreach

          $this->getEventsType()->addDocument(new \Elastica\Document('', array(
            'event_class' => $intent_data['event_class'],
            'event_tags' => $intent_data['event_tags'],
            'intent_timestamp' => $intent_data['@timestamp'],
            '@timestamp' => date(DateTime::RFC3339, $timestamp),
          )));

          $this->getIntentsType()->deleteDocument($intent);

          return; // Break here
        } catch(\Elastica\Exception\NotFoundException $e) {
          // Pass through if intent was not found
        } // try
      } // if

      if($on_intent_not_found instanceof Closure) {
        $event_data = $on_intent_not_found();

        if(is_array($event_data)) {
          switch(count($event_data)) {

            // We have just event class
            case 1:
              $event_data[] = null; // Tags
              $event_data[] = time(); // Timestamp

              break;

            // We have event class and tags
            case 2:
              $event_data[] = time(); // Timestamp
              break;

          } // switch

          if(count($event_data) == 3) {
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
      $response = $this->getIntentsType()->addDocument(new \Elastica\Document('', array(
        'event_class' => $event_class,
        'event_tags' => $event_tags,
        '@timestamp' => date(DateTime::RFC3339, $timestamp),
      )));

      if($response->isOk()) {
        $data = $response->getData();

        return $data['_id'];
      } else {
        return '';
      } // if
    } // recordIntent

    /**
     * Cached events type instance
     *
     * @var \Elastica\Type
     */
    private $events_type = false;

    /**
     * Return type for storing events
     *
     * @return \Elastica\Type
     */
    private function getEventsType() {
      if($this->events_type === false) {
        $this->events_type = $this->getIndex()->getType('events');
      } // if

      return $this->events_type;
    } // getEventsType

    /**
     * Cached intents type instance
     *
     * @var \Elastica\Type
     */
    private $intents_type = false;

    /**
     * Return types for storing intentns
     *
     * @return \Elastica\Type
     */
    private function getIntentsType() {
      if($this->intents_type === false) {
        $this->intents_type = $this->getIndex()->getType('intents');
      } // if

      return $this->intents_type;
    } // getIntentsType

    /**
     * Cached elastic search index
     *
     * @var \Elastica\Index
     */
    private $index = false;

    /**
     * Return index instance
     *
     * Return \Elastica\Index
     */
    private function &getIndex() {
      if($this->index === false) {
        $this->index = AngieApplication::elastica()->getClient()->getIndex($this->getIndexName());
      } // if

      return $this->index;
    } // getIndex

    /**
     * Cached index name
     *
     * @var string
     */
    private $index_name = false;

    /**
     * Return index name
     *
     * @return string
     */
    private function getIndexName() {
      if($this->index_name === false) {
        if(defined('TRACK_USER_BEHAVIOUR_INDEX_NAME') && TRACK_USER_BEHAVIOUR_INDEX_NAME) {
          $this->index_name = TRACK_USER_BEHAVIOUR_INDEX_NAME;
        } else {
          $this->index_name = Inflector::underscore(AngieApplication::getName()) . '_behaviour';
        } // if
      } // if

      return $this->index_name;
    } // getIndexName

    /**
     * Initialize index and types
     */
    function initForTesting() {
      if(AngieApplication::isInDevelopment()) {
        $this->index = AngieApplication::elastica()->getClient()->getIndex($this->getIndexName());
        $this->index->create(array(), true);

        $this->events_type = $this->index->getType('events');

        $mapping = new \Elastica\Type\Mapping($this->events_type);
        $mapping->setProperties(array(
          'event_class' => array('type' => 'string'),
          'event_tags' => array('type' => 'string'),
          'intent_timestamp' => array(
            'index' => 'not_analyzed',
            'type' => 'date',
          ),
          '@timestamp' => array(
            'index' => 'not_analyzed',
            'type' => 'date',
            //'format' => 'yyyy-MM-dd HH:mm:ss',
          ),
        ));
        $mapping->send();

        $this->intents_type = $this->index->getType('intents');

        $mapping = new \Elastica\Type\Mapping($this->intents_type);
        $mapping->setProperties(array(
          'event_class' => array('type' => 'string'),
          'event_tags' => array('type' => 'string'),
          '@timestamp' => array(
            'index' => 'not_analyzed',
            'type' => 'date',
            //'format' => 'yyyy-MM-dd HH:mm:ss',
          ),
        ));
        $mapping->send();
      } else {
        throw new NotImplementedError(__METHOD__, 'This method is available only during development');
      } // if
    } // initForTesting

  }