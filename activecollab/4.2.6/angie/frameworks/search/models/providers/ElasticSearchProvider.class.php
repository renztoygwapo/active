<?php

  // Include Elastica
  require_once ANGIE_PATH . '/vendor/elastica/init.php';

  /**
   * ElasticSearch powered search provider
   *
   * @package angie.frameworks.search
   * @subpackage models
   */
  class ElasticSearchProvider extends SearchProvider {

    /**
     * Return name of the search provider
     *
     * @return string
     */
    function getName() {
      return lang('Elastic Search');
    } // getName

    /**
     * Return search provider description
     *
     * @return string
     */
    function getDescription() {
      return lang('This engine uses one of more ElastiSearch nodes to index and query your data. This is recommended engine because it providers better search results than MySQL powered engine');
    } // getDescription

    /**
     * Return provider specific settings
     *
     * @return mixed
     */
    function getSettings() {
      return ConfigOptions::getValue(array(
        'elastic_search_hosts',
      ));
    } // getSettings

    /**
     * Return render settings template
     *
     * @return string
     */
    function getRenderSettingsTemplate() {
      return AngieApplication::getViewPath('_elastic_search_provider_settings', null, SEARCH_FRAMEWORK);
    } // getRenderSettingsTemplate

    /**
     * Set provider data from settings array that gets submitted
     *
     * @param array $settings
     */
    function setSettings($settings) {
      if(array_key_exists('elastic_search_hosts', $settings)) {
        ConfigOptions::setValue('elastic_search_hosts', $settings['elastic_search_hosts']);
      } // if
    } // setSettings

    /**
     * Query index for given search string
     *
     * @param IUser $user
     * @param SearchIndex $index
     * @param string $search_for
     * @param mixed $criterions
     * @return array
     */
    function query(IUser $user, SearchIndex $index, $search_for, $criterions = null) {
      pre_var_dump($this->elasticSearchGetType($index)->search($this->elasticSearchGetQuery($search_for, $criterions)));
    } // query

    /**
     * Query paginated
     *
     * @param IUser $user
     * @param SearchIndex $index
     * @param string $search_for
     * @param mixed $criterions
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function queryPaginated(IUser $user, SearchIndex $index, $search_for, $criterions = null, $page = 1, $per_page = 30) {
      $query = $this->elasticSearchGetQuery($search_for, $criterions);

      $query->setFrom(($page - 1) * $per_page);
      $query->setLimit($per_page);

      $search_result = $this->elasticSearchGetType($index)->search($query);
      $total_hits = $search_result->getTotalHits();

      if($total_hits > 0) {
        $items = array();

        foreach($search_result as $search_hit) {
          $hit_data = $search_hit->getData();

          $item = $index->loadItemDetails($user, $hit_data['item_type'], $hit_data['item_id']);

          if($item) {
            $items[] = $item;
          } // if
        } // foreach

        return array($items, $total_hits);
      } // if

      return array(array(), 0); // Empty
    } // queryPaginated

    /**
     * Return query instance based on given parameters
     *
     * @param $search_for
     * @param SearchCriterion[] $criterions
     * @return Elastica\Query
     */
    private function elasticSearchGetQuery($search_for, $criterions = null) {
      $query_string = new Elastica\Query\QueryString();

      $query_string->setDefaultOperator('AND');
      $query_string->setQuery($search_for);

      $query = new \Elastica\Query();
      $query->setQuery($query_string);

      $filter = new \Elastica\Filter\BoolOr();

      foreach($criterions as $criterion) {
        $term = new \Elastica\Filter\Term();
        $term->setTerm($criterion->getField(), $criterion->getValue());

        $filter->addFilter($term);
      } // foreach

      if($criterions) {
        foreach($criterions as $criterion) {
          $term = new \Elastica\Filter\MatchAll();
        } // foreach
      } // if

      // Default limitations
      $query->setFrom(0);
      $query->setLimit(100);

      return $query;
    } // elasticSearchGetQuery

    /**
     * Add or update item in the index
     *
     * @param string $index
     * @param string $item_type
     * @param integer $item_id
     * @param string $item_context
     * @param mixed $additional
     */
    function set($index, $item_type, $item_id, $item_context = null, $additional = null) {
      $properties = array(
        'item_type' => $item_type,
        'item_id' => $item_id,
        'item_context' => $item_context
      );

      if($additional && is_array($additional)) {
        $properties = array_merge($properties, $additional);
      } // if

      $this->elasticSearchGetType($index)->addDocument(new \Elastica\Document($this->elasticSearchGetId($item_type, $item_id), $properties));
    } // set

    /**
     * Remove given item from a given index
     *
     * @param mixed $index
     * @param string $item_type
     * @param integer $item_id
     */
    function remove($index, $item_type, $item_id) {
      $this->elasticSearchGetType($index)->deleteById($this->elasticSearchGetId($item_type, $item_id));
    } // remove

    /**
     * Clear given index
     *
     * @param string $index_name
     */
    function clear($index_name) {
      $this->elasticSearchGetType($index_name)->deleteByQuery('*'); // @TODO Don't know whether this works or not
    } // clear

    /**
     * Update item context in a given index
     *
     * @param SearchIndex $index
     * @param IObjectContext $item
     * @param string $old_context
     * @param string $new_context
     */
    function updateItemContext(SearchIndex $index, IObjectContext $item, $old_context, $new_context) {
      try {
        $type = $this->elasticSearchGetType($index);
        $document = $type->getDocument($this->elasticSearchGetId($item));

        if($document->getParam('item_context') != $new_context) {
          $document->setParam('item_context', $new_context);
          $type->updateDocument($document);
        } // if
      } catch(\Elastica\Exception\NotFoundException $e) {

      } // try
    } // updateItemContext

    /**
     * Initialise environment
     */
    function initializeEnvironment() {
      $this->elasticSearchGetIndex()->create(array(
//        'number_of_shards' => 4,
//        'number_of_replicas' => 1,
//        'analysis' => array(
//          'analyzer' => array(
//            'indexAnalyzer' => array(
//              'type' => 'custom',
//              'tokenizer' => 'standard',
//              'filter' => array('lowercase', 'mySnowball')
//            ),
//            'searchAnalyzer' => array(
//              'type' => 'custom',
//              'tokenizer' => 'standard',
//              'filter' => array('standard', 'lowercase', 'mySnowball')
//            )
//          ),
//          'filter' => array(
//            'mySnowball' => array(
//              'type' => 'snowball',
//              'language' => 'German'
//            )
//          )
//        )
      ), true);
    } // initializeEnvironment

    /**
     * Returns true if $index is initalized
     *
     * @param SearchIndex $index
     * @param boolean $use_cache
     * @return boolean
     */
    function isInitialized(SearchIndex $index, $use_cache = true) {
      return false;
    } // isInitialized

    /**
     * Initalize given index
     *
     * @param SearchIndex $index
     * @throws InvalidParamError
     */
    function initialize(SearchIndex $index) {
      $type = $this->elasticSearchGetType($index);

      $mapping = new \Elastica\Type\Mapping($type);

      $properties = array(
        'id' => array('type' => 'integer', 'include_in_all' => false),
        'item_type' => array('type' => 'string', 'include_in_all' => false),
        'item_id' => array('type' => 'integer', 'include_in_all' => false),
      );

      foreach($index->getFields() as $field_name => $field_type) {
        switch($field_type) {
          case SearchIndex::FIELD_NUMERIC:
            $properties[$field_name] = array(
              'type' => 'integer',
              'include_in_all' => false,
            );

            break;
          case SearchIndex::FIELD_DATE:
            $properties[$field_name] = array(
              'index' => 'not_analyzed',
              'type' => 'date',
              'format' => 'yyyy-MM-dd',
              'include_in_all' => false,
            );

            break;
          case SearchIndex::FIELD_DATETIME:
            $properties[$field_name] = array(
              'index' => 'not_analyzed',
              'type' => 'date',
              'format' => 'yyyy-MM-dd HH:mm:ss',
              'include_in_all' => false,
            );

            break;
          case SearchIndex::FIELD_STRING:
          case SearchIndex::FIELD_TEXT:
            $properties[$field_name] = array(
              'type' => 'string',
              'include_in_all' => true,
            );

            break;
          default:
            throw new InvalidParamError('field_type', $field_type, "'$field_type' is not a valid search index field type");
        } // switch
      } // foreach

      $mapping->setProperties($properties);
      $mapping->send();
    } // initialize

    /**
     * Tear down given search index
     *
     * @param SearchIndex $index
     */
    function tearDown(SearchIndex $index) {

    } // tearDown

    /**
     * Return total number of records in given index
     *
     * @param SearchIndex $index
     * @return integer
     */
    function countRecords(SearchIndex $index) {

    } // countRecords

    /**
     * Return file size of the index
     *
     * @param SearchIndex $index
     * @return integer
     */
    function calculateSize(SearchIndex $index) {
      return 0;
    } // calculateSize

    // ---------------------------------------------------
    //  Elastica Specific
    // ---------------------------------------------------

    /**
     * Return item ID based on item class and item ID
     *
     * @param ApplicationObject|string $item_type
     * @param integer $item_id
     * @return string
     */
    private function elasticSearchGetId($item_type, $item_id = null) {
      if($item_type instanceof ApplicationObject) {
        return get_class($item_type) . '-' . $item_type->getId();
      } else {
        return "{$item_type}-{$item_id}";
      } // if
    } // elasticSearchGetId

    /**
     * Return ElasticSearch client
     *
     * @var \Elastica\Client
     */
    private $elastic_search_client = false;

    /**
     * Return elasitca client instance
     *
     * @return \Elastica\Client
     */
    function elasticSearchGetClient() {
      if($this->elastic_search_client === false) {
        $hosts = array();
        $hosts_config_value = ConfigOptions::getValue('elastic_search_hosts');

        if($hosts_config_value) {
          foreach(explode("\n", $hosts_config_value) as $host) {
            $host = trim($host);

            if($host) {
              $parts = parse_url($host);

              $hosts[] = array(
                'host' => isset($parts['host']) ? $parts['host'] : 'localhost',
                'port' => isset($parts['port']) ? $parts['port'] : 9200,
              );
            } // if
          } // foreach
        } // if


        if(count($hosts) > 1) {
          $this->elastic_search_client = new \Elastica\Client(array(
            'servers' => $hosts, // Multiple servers
          ));
        } elseif(count($hosts) == 1) {
          $this->elastic_search_client = new \Elastica\Client($hosts[0]); // Single server
        } else {
          $this->elastic_search_client = new \Elastica\Client();
        } // if
      } // if

      return $this->elastic_search_client;
    } // getClient

    /**
     * Main elasict search index for this application
     *
     * @var \Elastica\Index
     */
    private $elastic_search_index = false;

    /**
     * Return ElasticSearch index
     *
     * @return \Elastica\Index
     */
    function elasticSearchGetIndex() {
      if($this->elastic_search_index === false) {
        $this->elastic_search_index = $this->elasticSearchGetClient()->getIndex($this->elasticSearchGetIndexName());
      } // if

      return $this->elastic_search_index;
    } // getIndex

    /**
     * Return type instance for a given index
     *
     * @param SearchIndex|string $index
     * @return \Elastica\Type
     */
    function elasticSearchGetType(SearchIndex $index) {
      $index_name = $index instanceof SearchIndex ? $index->getShortName() : $index;

      return $this->elasticSearchGetIndex()->getType($index_name);
    } // elasticSearchGetType

    /**
     * Cached index name
     *
     * @var string
     */
    private $elastic_search_index_name = false;

    /**
     * Return index name for this application instance
     *
     * @return string
     */
    private function elasticSearchGetIndexName() {
      if($this->elastic_search_index_name === false) {
        $this->elastic_search_index_name = ELASTIC_SEARCH_INDEX_NAME ? ELASTIC_SEARCH_INDEX_NAME : Inflector::underscore(AngieApplication::getName());
      } // if

      return $this->elastic_search_index_name;
    } // elasticSearchGetIndexName

  }