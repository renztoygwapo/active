<?php

  /**
   * on_search_indices event handler
   * 
   * @package activeCollab.modules.documents
   * @subpackage handlers
   */

  /**
   * Register search index
   * 
   * @param array $indices
   * @param SearchProvider $provider
   */
  function documents_handle_on_search_indices(&$indices, SearchProvider &$provider) {
    $indices['documents'] = new DocumentsSearchIndex($provider);
  } // documents_handle_on_search_indices