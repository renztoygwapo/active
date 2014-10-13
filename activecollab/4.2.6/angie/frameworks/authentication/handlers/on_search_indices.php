<?php

  /**
   * on_search_indices event handler
   * 
   * @package angie.frameworks.authentication
   * @subpackage handlers
   */

  /**
   * Register search index
   * 
   * @param array $indices
   * @param SearchProvider $provider
   */
  function authentication_handle_on_search_indices(&$indices, SearchProvider &$provider) {
    $indices['users'] = new UsersSearchIndex($provider);
  } // authentication_handle_on_search_indices