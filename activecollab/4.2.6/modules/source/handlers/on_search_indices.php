<?php

  /**
   * on_search_indices event handler
   * 
   * @package activeCollab.modules.source
   * @subpackage handlers
   */

  /**
   * Register search index
   * 
   * @param array $indices
   * @param SearchProvider $provider
   */
  function source_handle_on_search_indices(&$indices, SearchProvider &$provider) {
    $indices['source'] = new SourceSearchIndex($provider);
  } // source_handle_on_search_indices