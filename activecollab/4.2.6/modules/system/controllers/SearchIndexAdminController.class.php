<?php

  // Build on top of framework level controller
  AngieApplication::useController('fw_search_index_admin', SEARCH_FRAMEWORK);

  /**
   * Application level search index administration controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class SearchIndexAdminController extends FwSearchIndexAdminController {
  
  }