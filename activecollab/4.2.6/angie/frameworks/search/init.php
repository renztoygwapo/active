<?php

  /**
   * Search framework initialization file
   * 
   * @package angie.frameworks.search
   */
  
  const SEARCH_FRAMEWORK = 'search';
  const SEARCH_FRAMEWORK_PATH = __DIR__;

  defined('SEARCH_FRAMEWORK_INJECT_INTO') or define('SEARCH_FRAMEWORK_INJECT_INTO', 'system');
  defined('SEARCH_FRAMEWORK_ADMIN_ROUTE_BASE') or define('SEARCH_FRAMEWORK_ADMIN_ROUTE_BASE', 'admin');
  defined('SEARCH_FRAMEWORK_BACKEND_SEARCH_BASE') or define('SEARCH_FRAMEWORK_BACKEND_SEARCH_BASE', 'backend');

  defined('ELASTIC_SEARCH_INDEX_NAME') or define('ELASTIC_SEARCH_INDEX_NAME', null);
  defined('ELASTIC_SEARCH_NUMBER_OF_SHARDS') or define('ELASTIC_SEARCH_NUMBER_OF_SHARDS', 6);
  defined('ELASTIC_SEARCH_NUMBER_OF_REPLICAS') or define('ELASTIC_SEARCH_NUMBER_OF_REPLICAS', 1);

  AngieApplication::setForAutoload(array(
    'Search' => SEARCH_FRAMEWORK_PATH . '/models/Search.class.php',
  
    'SearchItemsCriteria' => SEARCH_FRAMEWORK_PATH . '/models/criteria/SearchItemsCriteria.class.php',
    'IncludeSearchItemsCriteria' => SEARCH_FRAMEWORK_PATH . '/models/criteria/IncludeSearchItemsCriteria.class.php',
    'ExcludeSearchItemsCriteria' => SEARCH_FRAMEWORK_PATH . '/models/criteria/ExcludeSearchItemsCriteria.class.php',
    'SearchCriterion' => SEARCH_FRAMEWORK_PATH . '/models/criteria/SearchCriterion.class.php',
  
    'ISearchItem' => SEARCH_FRAMEWORK_PATH . '/models/ISearchItem.class.php',
    'ISearchItemImplementation' => SEARCH_FRAMEWORK_PATH . '/models/ISearchItemImplementation.class.php',
  
    'SearchIndex' => SEARCH_FRAMEWORK_PATH . '/models/indexes/SearchIndex.class.php',
  
    'SearchFilter' => SEARCH_FRAMEWORK_PATH . '/models/filters/SearchFilter.class.php',
    'NumberSearchFilter' => SEARCH_FRAMEWORK_PATH . '/models/filters/NumberSearchFilter.class.php',
    'DateSearchFilter' => SEARCH_FRAMEWORK_PATH . '/models/filters/DateSearchFilter.class.php',
    'EnumerableSearchFilter' => SEARCH_FRAMEWORK_PATH . '/models/filters/EnumerableSearchFilter.class.php',
  
    'SearchProvider' => SEARCH_FRAMEWORK_PATH . '/models/providers/SearchProvider.class.php',
    'MySqlSearchProvider' => SEARCH_FRAMEWORK_PATH . '/models/providers/MySqlSearchProvider.class.php', 
    'ElasticSearchProvider' => SEARCH_FRAMEWORK_PATH . '/models/providers/ElasticSearchProvider.class.php',
  ));