<?php

  /**
   * on_search_indices event handler
   * 
   * @package angie.frameworks.help
   * @subpackage handlers
   */

  /**
   * Register search index
   * 
   * @param array $indices
   * @param SearchProvider $provider
   */
  function help_handle_on_search_indices(&$indices, SearchProvider &$provider) {
    $indices['help'] = new HelpSearchIndex($provider);
  } // help_handle_on_search_indices