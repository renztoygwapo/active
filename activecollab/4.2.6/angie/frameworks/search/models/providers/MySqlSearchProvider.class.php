<?php

  /**
   * Search provider that uses MySQL for content indexing
   * 
   * @package angie.frameworks.search
   * @subpackage models
   */
  class MySqlSearchProvider extends SearchProvider {

    /**
     * Return name of the search provider
     *
     * @return string
     */
    function getName() {
      return lang('Basic Search');
    } // getName

    /**
     * Return search provider description
     *
     * @return string
     */
    function getDescription() {
      return lang('Basic search uses MySQL build in full-text search functionality. This engine is default because it is available everywhere, but we recommend that you use any of the other engines when they are available on your platform');
    } // getDescription
    
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
      $conditions = $this->prepareConditions($user, $index, $search_for, $criterions);
      
      if($conditions !== false) {
        $count = (integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . $this->getTableName($index) . " WHERE $conditions");
        
        if($count) {
          $result = array();

          $order_by = $this->prepareOrderBy($index);
          
          foreach(DB::execute('SELECT item_type, item_id FROM ' . $this->getTableName($index) . " WHERE $conditions $order_by LIMIT 0, 100") as $row) {
            $item = $index->loadItemDetails($user, $row['item_type'], $row['item_id']);

            if($item) {
              $result[] = $item;
            } // if
          } // foreach
          
          return $result;
        } // if
      } // if
      
      return array();
    } // query
    
    /**
     * Return pagianted result
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
      $conditions = $this->prepareConditions($user, $index, $search_for, $criterions);
      
      if($conditions !== false) {
        $count = (integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . $this->getTableName($index) . " WHERE $conditions");
        
        if($count) {
          $items = array();

          $order_by = $this->prepareOrderBy($index);

          $down_limit = ($page - 1) * $per_page;
          foreach(DB::execute('SELECT item_type, item_id FROM ' . $this->getTableName($index) . " WHERE $conditions $order_by LIMIT $down_limit, $per_page") as $row) {
            $item = $index->loadItemDetails($user, $row['item_type'], $row['item_id']);

            if($item) {
              $items[] = $item;
            } // if
          } // foreach
          
          return array($items, $count);
        } // if
      } // if
      
      return array(array(), 0);
    } // queryPaginated
    
    /**
     * Return number of items in the interface
     *
     * @param IUser $user
     * @param SearchIndex $index
     * @param string $search_for
     * @param null|array $criterions
     * @return int
     */
    function count(IUser $user, SearchIndex $index, $search_for, $criterions = null) {
      $conditions = $this->prepareConditions($user, $index, $search_for, $criterions);
      
      if($conditions === false) {
        return 0;
      } else {
        return (integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . $this->getTableName($index) . " WHERE $conditions");
      } // if
    } // count
    
    /**
     * Convert criterion to condition
     *
     * @param SearchIndex $index
     * @param SearchCriterion $criterion
     * @return string
     * @throws InvalidParamError
     */
    protected function criterionToCondition(SearchIndex $index, SearchCriterion $criterion) {
      switch($criterion->getCriterion()) {
        
        // Match equal
        case SearchCriterion::IS:
          if(is_array($criterion->getValue()) && count($criterion->getValue()) > 1) {
            return DB::escapeFieldName($criterion->getField()) . ' IN (' . DB::escape($criterion->getValue()) . ')';
          } else {
            return DB::escapeFieldName($criterion->getField()) . ' = ' . DB::escape($criterion->getValue());
          } // if
          
        // Match not equal
        case SearchCriterion::IS_NOT:
          if(is_array($criterion->getValue()) && count($criterion->getValue()) > 1) {
            return DB::escapeFieldName($criterion->getField()) . ' NOT IN (' . DB::escape($criterion->getValue()) . ')';
          } else {
            return DB::escapeFieldName($criterion->getField()) . ' <> ' . DB::escape($criterion->getValue());
          } // if
          
        // Match greater than
        case SearchCriterion::GREATER:
          return DB::escapeFieldName($criterion->getField()) . ' > ' . DB::escape($criterion->getValue());
          
        // Match greater than or equal
        case SearchCriterion::GREATER_OR_EQUAL:
          return DB::escapeFieldName($criterion->getField()) . ' >= ' . DB::escape($criterion->getValue());
          
        // Match smaller than
        case SearchCriterion::SMALLER:
          return DB::escapeFieldName($criterion->getField()) . ' < ' . DB::escape($criterion->getValue());
          
        // Match smaller than or equal
        case SearchCriterion::SMALLER_OR_EQUAL:
          return DB::escapeFieldName($criterion->getField()) . ' <= ' . DB::escape($criterion->getValue());
          
        // Like
        case SearchCriterion::LIKE:
          return DB::escapeFieldName($criterion->getField()) . ' LIKE ' . DB::escape($criterion->getValue());
          
        // Invalid criterion type
        default:
          throw new InvalidParamError('criterion', "'" . $criterion->getCriterion() . "' is not a valid search criterion");
      } // switch
    } // criterionToCondition
    
    /**
     * Add or update item in the index
     * 
     * @param string $index
     * @param string $item_type
     * @param int|string $item_id
     * @param string $item_context
     * @param null|array $additional
     */
    function set($index, $item_type, $item_id, $item_context = null, $additional = null) {
      $fields = array('item_type', 'item_id', 'item_context');
      $values = array(DB::escape($item_type), DB::escape($item_id), DB::escape($item_context));
      
      if($additional) {
        foreach($additional as $k => $v) {
          $fields[] = DB::escapeFieldName($k);
          $values[] = DB::escape($v); 
        } // foreach
      } // if
      
      DB::execute('REPLACE INTO ' . $this->getTableName($index) . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')');
    } // set
    
    /**
     * Remove given item from a given index
     * 
     * @param mixed $index
     * @param string $item_type
     * @param int|string $item_id
     */
    function remove($index, $item_type, $item_id) {
      DB::execute('DELETE FROM ' . $this->getTableName($index) . ' WHERE item_type = ? AND item_id = ?', $item_type, $item_id);
    } // remove

    /**
     * Prepare order by based on search index
     *
     * @param SearchIndex $index
     * @return string
     */
    function prepareOrderBy(SearchIndex $index) {
      $priority_fields = $index->getPriorityFields();
      if (!count($priority_fields)) {
        return '';
      } // if

      $order_by_expressions = array();
      foreach ($priority_fields as $priority_field) {
        $order_by_expressions[] = '(' . $priority_field . ' * 2)';
      } // foreach

      return 'ORDER BY ' . join(' + ', $order_by_expressions);
    } // prepareOrderBy
    
    /**
     * Prepare conditions based on input data
     * 
     * @param IUser $user
     * @param SearchIndex $index
     * @param string $search_for
     * @param SearchCriterion[] $criterions
     * @return string
     */
    protected function prepareConditions(IUser $user, SearchIndex $index, $search_for, $criterions = null) {
      $primary_conditions = array();
      $extension_conditions = array();
      
      // ---------------------------------------------------
      //  User filter
      // ---------------------------------------------------
      
      $user_filter = $index->getUserFilter($user);
      
      // Abort, user can't see anything in this index
      if($user_filter === false) {
        return false;
        
      // Single criterion
      } elseif($user_filter instanceof SearchCriterion) {
        $primary_conditions[] = $this->criterionToCondition($index, $user_filter);
        
      // A list of criterions
      } elseif(is_foreachable($user_filter)) {
        foreach($user_filter as $k => $v) {
          
          // Include or exclude criteria
          if($v instanceof SearchItemsCriteria) {
            $user_subfilters = array();
            
            foreach($v->getCriterions() as $subfilter) {
              $user_subfilters[] = $this->criterionToCondition($index, $subfilter);
            } // foreach

            if($v instanceof IncludeSearchItemsCriteria) {
              $user_filter[$k] = '(' . implode(' OR ', $user_subfilters) . ')';
            } else {
              $user_filter[$k] = 'NOT (' . implode(' OR ', $user_subfilters) . ')';
            } // if
            
          // Array of criterions
          } elseif(is_array($v)) {
            $user_subfilters = array();
            
            foreach($v as $subfilter) {
              $user_subfilters[] = $this->criterionToCondition($index, $subfilter);
            } // foreach 
            
            $user_filter[$k] = '(' . implode(' OR ', $user_subfilters) . ')';
            
          // Single criterion
          } else {
            $user_filter[$k] = $this->criterionToCondition($index, $v);
          } // if
          
        } // foreach
        
        $primary_conditions[] = '(' . implode(' AND ', $user_filter) . ')';
      } // if
      
      // ---------------------------------------------------
      //  Query
      // ---------------------------------------------------
      
      if($search_for) {
        if((str_starts_with($search_for, "'") && str_ends_with($search_for, "'")) || (str_starts_with($search_for, '"') && str_ends_with($search_for, '"'))) {
          $term_modifier = '';
        } else {
          $term_modifier = '*';
        } // if

        $search_for_parts = explode(' ', $search_for);
        $stop_words = array();
        foreach($search_for_parts as $k => $v) {
          $part = strtolower(trim(trim($v, "'"), '"'));

          if($this->isStopword($part)) {
            $stop_words[] = $part;
            unset($search_for_parts[$k]);
          } else {
            $search_for_parts[$k] = "+{$part}{$term_modifier}";
          } // if
        } // foreach

        if (count($search_for_parts) < 1 && count($stop_words) < 1) {
          return false; // All terms are stopwords
        } // if
        
        $search_for_term = implode(' ', $search_for_parts);
        
        $fulltext_fields = array();
        
        foreach($index->getFields() as $field_name => $field_type) {
          if($field_type == SearchIndex::FIELD_STRING || $field_type == SearchIndex::FIELD_TEXT) {
            $fulltext_fields[] = DB::escapeFieldName($field_name);
          } // if
        } // foreach
        
        if(count($fulltext_fields)) {
          if (count($search_for_parts)) {
            $primary_conditions[] = DB::prepare('MATCH (' . implode(', ', $fulltext_fields) . ') AGAINST (? IN BOOLEAN MODE)', $search_for_term);
          } // if

          if(count($stop_words)) {
            foreach($stop_words as $stop_word) {
              $fulltext_like_or = array();

              foreach($fulltext_fields as $fulltext_field) {
                $fulltext_like_or[] = DB::prepare("$fulltext_field LIKE '%" . str_replace("'", "\\'", $stop_word) . "%'");
              } // foreach

              $primary_conditions[] = '(' . implode(' OR ', $fulltext_like_or) . ')';
            } // foreach
          } // if
        } // if
      } // if
      
      // ---------------------------------------------------
      //  Additional constraints
      // ---------------------------------------------------
      
      if($criterions) {
        foreach($criterions as $criterion) {
          if($criterion->getType() == SearchCriterion::EXTEND_RESULT) {
            $extension_conditions[] = $this->criterionToCondition($index, $criterion);
          } else {
            $primary_conditions[] = $this->criterionToCondition($index, $criterion);
          } // if
        } // foreach
      } // if
      
      // ---------------------------------------------------
      //  Mix 'em up
      // ---------------------------------------------------
      
      $conditions = '';
      
      if(count($primary_conditions)) {
        $conditions = '(' . implode(' AND ', $primary_conditions) . ')';
      } // if
      
      if($extension_conditions) {
        $conditions .= ' OR ' . implode(' OR ', $extension_conditions);
      } // if
      
      return $conditions;
    } // prepareConditions
    
    // ---------------------------------------------------
    //  Index management
    // ---------------------------------------------------

    /**
     * Initialize environment
     */
    function initializeEnvironment() {
      // Environment already initialized (we already have DB connection)
    } // initializeEnvironment
    
    /**
     * Cached results of isInitalized function
     *
     * @var array
     */
    private $is_initialized = array();
    
    /**
     * Returns true if $index is initialized
     * 
     * @param SearchIndex $index
     * @param boolean $use_cache
     * @return boolean
     */
    function isInitialized(SearchIndex $index, $use_cache = true) {
      $index_name = $index->getShortName();
      
      if(!$use_cache || !array_key_exists($index_name, $this->is_initialized)) {
        $this->is_initialized[$index_name] = DB::tableExists($this->getTableName($index));
      } // if
      
      return $this->is_initialized[$index_name];
    } // isInitialized
    
    /**
     * Initalize given index
     * 
     * @param SearchIndex $index
     * @throws InvalidParamError
     */
    function initialize(SearchIndex $index) {
      $item_id = $index->getIdType() == SearchIndex::ID_NUMERIC ? "item_id int(10) unsigned NOT NULL default '0'" : "item_id varchar(255) NOT NULL default ''";

      $fields = array(
        "item_type varchar(50) NOT NULL default ''", 
        $item_id,
      	"item_context varchar(255) default NULL", 
      );
      
      $indices = array(
        'KEY item_context (item_context)', 
        'PRIMARY KEY (item_type, item_id)'
      );
      $fulltext_fields = array(); // Fields that we'll use to create fulltext index
      
      foreach($index->getFields() as $field_name => $field_type) {
        $escaped_field_name = DB::escapeFieldName($field_name);
        
        switch($field_type) {
          case SearchIndex::FIELD_NUMERIC:
            $fields[] = "$escaped_field_name int";
            $indices[] = "KEY $escaped_field_name ($escaped_field_name)";
            
            break;
          case SearchIndex::FIELD_DATE:
            $fields[] = "$escaped_field_name date";
            $indices[] = "KEY $escaped_field_name ($escaped_field_name)";
            
            break;
          case SearchIndex::FIELD_DATETIME:
            $fields[] = "$escaped_field_name datetime";
            $indices[] = "KEY $escaped_field_name ($escaped_field_name)";
            
            break;
          case SearchIndex::FIELD_STRING:
            $fields[] = "$escaped_field_name varchar(255)";
            $fulltext_fields[] = $escaped_field_name;
            
            break;
          case SearchIndex::FIELD_TEXT:
            $fields[] = "$escaped_field_name longtext";
            $fulltext_fields[] = $escaped_field_name;
            
            break;
          default:
            throw new InvalidParamError('field_type', $field_type, "'$field_type' is not a valid search index field type");
        } // switch
      } // foreach
      
      if(count($fulltext_fields) > 0) {
        $indices[] = 'FULLTEXT KEY content (' . implode(', ', $fulltext_fields) . ')';
      } // if
      
      $table_name = $this->getTableName($index);
      
      if(DB::tableExists($table_name)) {
        DB::dropTable($table_name);
      } // if
      
      $escaped_table_name = DB::escapeTableName($table_name);
      
      DB::execute("CREATE TABLE $escaped_table_name (
        " . implode(', ', $fields) . ", 
        " . implode(', ', $indices) . "
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci");
      
      $this->is_initialized[$index->getShortName()] = true;
    } // initialize
    
    /**
     * Tear down given search index
     * @param SearchIndex $index
     */
    function tearDown(SearchIndex $index) {
      $index_name = $index->getShortName();
      $table_name = $this->getTableName($index);
      
      if(DB::tableExists($table_name)) {
        DB::dropTable($table_name);
      } // if
      
      if(isset($this->is_initialized[$index_name])) {
        unset($this->is_initialized[$index_name]);
      } // if
    } // tearDown
    
    /**
     * Return total number of records in given index
     * 
     * @param SearchIndex $index
     * @return integer
     */
    function countRecords(SearchIndex $index) {
      return (integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . $this->getTableName($index));
    } // countRecords
    
    /**
     * Return file size of the index
     * 
     * @param SearchIndex $index
     * @return integer
     */
    function calculateSize(SearchIndex $index) {
      $row = DB::executeFirstRow('SHOW TABLE STATUS LIKE ?', $this->getTableName($index));
      
      if($row && isset($row['Data_length']) && isset($row['Index_length'])) {
        return $row['Data_length'] + $row['Index_length'];
      } else {
        return 0;
      } // if
    } // calculateSize
    
    /**
     * Clear given index
     * 
     * $index can be index name or instance of SearchIndex class
     * 
     * @param mixed $index
     */
    function clear($index) {
      DB::execute('TRUNCATE TABLE ' . $this->getTableName($index));
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
      $table_name = $this->getTableName($index);
      
      $rows = DB::execute("SELECT item_type, item_id, item_context FROM $table_name WHERE item_context LIKE ?", "$old_context%");
      if($rows) {
        foreach($rows as $row) {
          DB::execute("UPDATE $table_name SET item_context = ? WHERE item_type = ? AND item_id = ?", str_replace($old_context, $new_context, $row['item_context']), $row['item_type'], $row['item_id']);
        } // foreach
      } // if
    } // updateItemContext
    
    /**
     * Return table name based on index name
     * 
     * $index can be a SearchIndex instance or index name
     * 
     * @param mixed $index
     * @return string
     * @throws InvalidInstanceError
     */
    protected function getTableName($index) {
      if($index instanceof SearchIndex) {
        $index_name = $index->getShortName();
      } elseif(is_string($index)) {
        $index_name = $index;
      } else {
        throw new InvalidInstanceError('index', $index, 'SearchIndex');
      } // if
      
      return TABLE_PREFIX . "search_index_for_{$index_name}"; 
    } // getTableName
    
    // ---------------------------------------------------
    //  Tips
    // ---------------------------------------------------
    
    /**
     * Cached min keyword lenght value
     *
     * @var integer
     */
    private $min_keyword_length = false;
    
    /**
     * Return min keyword lenght
     * 
     * @return integer
     */
    function getMinKeywordLength() {
      if($this->min_keyword_length === false) {
        $row = DB::executeFirstRow("SHOW VARIABLES LIKE 'ft_min_word_len'");
        if($row && isset($row['Value']) && $row['Value']) {
          $this->min_keyword_length = (integer) $row['Value'];
        } else {
          $this->min_keyword_length = 4; // Not found? Use default value (from http://dev.mysql.com/doc/refman/5.1/en/server-system-variables.html#sysvar_ft_min_word_len)
        } // if
      } // if
      
      return $this->min_keyword_length;
    } // getMinKeywordLength

    private $stopwords = array(
      'a\'s',
      'able',
      'about',
      'above',
      'according',
      'accordingly',
      'across',
      'actually',
      'after',
      'afterwards',
      'again',
      'against',
      'ain\'t',
      'all',
      'allow',
      'allows',
      'almost',
      'alone',
      'along',
      'already',
      'also',
      'although',
      'always',
      'am',
      'among',
      'amongst',
      'an',
      'and',
      'another',
      'any',
      'anybody',
      'anyhow',
      'anyone',
      'anything',
      'anyway',
      'anyways',
      'anywhere',
      'apart',
      'appear',
      'appreciate',
      'appropriate',
      'are',
      'aren\'t',
      'around',
      'as',
      'aside',
      'ask',
      'asking',
      'associated',
      'at',
      'available',
      'away',
      'awfully',
      'be',
      'became',
      'because',
      'become',
      'becomes',
      'becoming',
      'been',
      'before',
      'beforehand',
      'behind',
      'being',
      'believe',
      'below',
      'beside',
      'besides',
      'best',
      'better',
      'between',
      'beyond',
      'both',
      'brief',
      'but',
      'by',
      'c\'mon',
      'c\'s',
      'came',
      'can',
      'can\'t',
      'cannot',
      'cant',
      'cause',
      'causes',
      'certain',
      'certainly',
      'changes',
      'clearly',
      'co',
      'com',
      'come',
      'comes',
      'concerning',
      'consequently',
      'consider',
      'considering',
      'contain',
      'containing',
      'contains',
      'corresponding',
      'could',
      'couldn\'t',
      'course',
      'currently',
      'definitely',
      'described',
      'despite',
      'did',
      'didn\'t',
      'different',
      'do',
      'does',
      'doesn\'t',
      'doing',
      'don\'t',
      'done',
      'down',
      'downwards',
      'during',
      'each',
      'edu',
      'eg',
      'eight',
      'either',
      'else',
      'elsewhere',
      'enough',
      'entirely',
      'especially',
      'et',
      'etc',
      'even',
      'ever',
      'every',
      'everybody',
      'everyone',
      'everything',
      'everywhere',
      'ex',
      'exactly',
      'example',
      'except',
      'far',
      'few',
      'fifth',
      'first',
      'five',
      'followed',
      'following',
      'follows',
      'for',
      'former',
      'formerly',
      'forth',
      'four',
      'from',
      'further',
      'furthermore',
      'get',
      'gets',
      'getting',
      'given',
      'gives',
      'go',
      'goes',
      'going',
      'gone',
      'got',
      'gotten',
      'greetings',
      'had',
      'hadn\'t',
      'happens',
      'hardly',
      'has',
      'hasn\'t',
      'have',
      'haven\'t',
      'having',
      'he',
      'he\'s',
      'hello',
      'help',
      'hence',
      'her',
      'here',
      'here\'s',
      'hereafter',
      'hereby',
      'herein',
      'hereupon',
      'hers',
      'herself',
      'hi',
      'him',
      'himself',
      'his',
      'hither',
      'hopefully',
      'how',
      'howbeit',
      'however',
      'i\'d',
      'i\'ll',
      'i\'m',
      'i\'ve',
      'ie',
      'if',
      'ignored',
      'immediate',
      'in',
      'inasmuch',
      'inc',
      'indeed',
      'indicate',
      'indicated',
      'indicates',
      'inner',
      'insofar',
      'instead',
      'into',
      'inward',
      'is',
      'isn\'t',
      'it',
      'it\'d',
      'it\'ll',
      'it\'s',
      'its',
      'itself',
      'just',
      'keep',
      'keeps',
      'kept',
      'know',
      'known',
      'knows',
      'last',
      'lately',
      'later',
      'latter',
      'latterly',
      'least',
      'less',
      'lest',
      'let',
      'let\'s',
      'like',
      'liked',
      'likely',
      'little',
      'look',
      'looking',
      'looks',
      'ltd',
      'mainly',
      'many',
      'may',
      'maybe',
      'me',
      'mean',
      'meanwhile',
      'merely',
      'might',
      'more',
      'moreover',
      'most',
      'mostly',
      'much',
      'must',
      'my',
      'myself',
      'name',
      'namely',
      'nd',
      'near',
      'nearly',
      'necessary',
      'need',
      'needs',
      'neither',
      'never',
      'nevertheless',
      'new',
      'next',
      'nine',
      'no',
      'nobody',
      'non',
      'none',
      'noone',
      'nor',
      'normally',
      'not',
      'nothing',
      'novel',
      'now',
      'nowhere',
      'obviously',
      'of',
      'off',
      'often',
      'oh',
      'ok',
      'okay',
      'old',
      'on',
      'once',
      'one',
      'ones',
      'only',
      'onto',
      'or',
      'other',
      'others',
      'otherwise',
      'ought',
      'our',
      'ours',
      'ourselves',
      'out',
      'outside',
      'over',
      'overall',
      'own',
      'particular',
      'particularly',
      'per',
      'perhaps',
      'placed',
      'please',
      'plus',
      'possible',
      'presumably',
      'probably',
      'provides',
      'que',
      'quite',
      'qv',
      'rather',
      'rd',
      're',
      'really',
      'reasonably',
      'regarding',
      'regardless',
      'regards',
      'relatively',
      'respectively',
      'right',
      'said',
      'same',
      'saw',
      'say',
      'saying',
      'says',
      'second',
      'secondly',
      'see',
      'seeing',
      'seem',
      'seemed',
      'seeming',
      'seems',
      'seen',
      'self',
      'selves',
      'sensible',
      'sent',
      'serious',
      'seriously',
      'seven',
      'several',
      'shall',
      'she',
      'should',
      'shouldn\'t',
      'since',
      'six',
      'so',
      'some',
      'somebody',
      'somehow',
      'someone',
      'something',
      'sometime',
      'sometimes',
      'somewhat',
      'somewhere',
      'soon',
      'sorry',
      'specified',
      'specify',
      'specifying',
      'still',
      'sub',
      'such',
      'sup',
      'sure',
      't\'s',
      'take',
      'taken',
      'tell',
      'tends',
      'th',
      'than',
      'thank',
      'thanks',
      'thanx',
      'that',
      'that\'s',
      'thats',
      'the',
      'their',
      'theirs',
      'them',
      'themselves',
      'then',
      'thence',
      'there',
      'there\'s',
      'thereafter',
      'thereby',
      'therefore',
      'therein',
      'theres',
      'thereupon',
      'these',
      'they',
      'they\'d',
      'they\'ll',
      'they\'re',
      'they\'ve',
      'think',
      'third',
      'this',
      'thorough',
      'thoroughly',
      'those',
      'though',
      'three',
      'through',
      'throughout',
      'thru',
      'thus',
      'to',
      'together',
      'too',
      'took',
      'toward',
      'towards',
      'tried',
      'tries',
      'truly',
      'try',
      'trying',
      'twice',
      'two',
      'un',
      'under',
      'unfortunately',
      'unless',
      'unlikely',
      'until',
      'unto',
      'up',
      'upon',
      'us',
      'use',
      'used',
      'useful',
      'uses',
      'using',
      'usually',
      'value',
      'various',
      'very',
      'via',
      'viz',
      'vs',
      'want',
      'wants',
      'was',
      'wasn\'t',
      'way',
      'we',
      'we\'d',
      'we\'ll',
      'we\'re',
      'we\'ve',
      'welcome',
      'well',
      'went',
      'were',
      'weren\'t',
      'what',
      'what\'s',
      'whatever',
      'when',
      'whence',
      'whenever',
      'where',
      'where\'s',
      'whereafter',
      'whereas',
      'whereby',
      'wherein',
      'whereupon',
      'wherever',
      'whether',
      'which',
      'while',
      'whither',
      'who',
      'who\'s',
      'whoever',
      'whole',
      'whom',
      'whose',
      'why',
      'will',
      'willing',
      'wish',
      'with',
      'within',
      'without',
      'won\'t',
      'wonder',
      'would',
      'wouldn\'t',
      'yes',
      'yet',
      'you',
      'you\'d',
      'you\'ll',
      'you\'re',
      'you\'ve',
      'your',
      'yours',
      'yourself',
      'yourselves',
      'zero',
    );

    /**
     * Returns true if $word is a stopword
     *
     * @param string $word
     * @return bool
     */
    function isStopword($word) {
      return in_array($word, $this->stopwords);
    } // isStopword
    
    /**
     * Returns true if this engine has any tips
     */
    function hasTips() {
      return true;
    } // hasTips
    
    /**
     * Return tips for this particular search engine
     * 
     * @return array
     */
    function getTips() {
      return array(
        lang('Single search keyword should be at least :min letters long. This limitation does not apply to shortcut keywords: task ID-s, commit ID-s etc', array('min' => $this->getMinKeywordLength())), 
        lang('Search ignores common words such are: value, color etc. Full list of ignored words is available here: http://dev.mysql.com/doc/refman/5.1/en/fulltext-stopwords.html'), 
      );
    } // getTips
  
  }