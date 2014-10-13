<?php

  /**
   * Help framework definition
   *
   * @package angie.frameworks.help
   */
  class HelpFramework extends AngieFramework {
    
    /**
     * Framework name
     *
     * @var string
     */
    protected $name = 'help';
    
		/**
     * Define module routes
     */
    function defineRoutes() {
      Router::map('help', 'help', array('controller' => 'help', 'module' => HELP_FRAMEWORK_INJECT_INTO));
      Router::map('help_popup', 'help/popup', array('controller' => 'help', 'action' => 'popup', 'module' => HELP_FRAMEWORK_INJECT_INTO));
      Router::map('help_search', 'help/search', array('controller' => 'help', 'action' => 'search', 'module' => HELP_FRAMEWORK_INJECT_INTO));

      Router::map('help_whats_new', 'help/whats-new', array('controller' => 'help_whats_new', 'module' => HELP_FRAMEWORK_INJECT_INTO));
      Router::map('help_whats_new_article', 'help/whats-new/:article_name', array('controller' => 'help_whats_new', 'action' => 'article', 'module' => HELP_FRAMEWORK_INJECT_INTO));

      Router::map('help_books', 'help/books', array('controller' => 'help_books', 'module' => HELP_FRAMEWORK_INJECT_INTO));
      Router::map('help_book', 'help/books/:book_name', array('controller' => 'help_books', 'action' => 'book', 'module' => HELP_FRAMEWORK_INJECT_INTO));

      Router::map('help_book_page', 'help/books/:book_name/pages/:page_name', array('controller' => 'help_books', 'action' => 'page', 'module' => HELP_FRAMEWORK_INJECT_INTO));

      Router::map('help_videos', 'help/videos', array('controller' => 'help_videos', 'module' => HELP_FRAMEWORK_INJECT_INTO));
      Router::map('help_video', 'help/videos/:video_name', array('controller' => 'help_videos', 'action' => 'video', 'module' => HELP_FRAMEWORK_INJECT_INTO));

      Router::map('help_search_index_admin_build', 'admin/search/help/build', array('controller' => 'help_search_index_admin', 'action' => 'build', 'module' => HELP_FRAMEWORK_INJECT_INTO, 'search_index_name' => 'help'));
    } // defineRoutes

    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_search_indices', 'on_search_indices');
    } // defineHandlers
    
  }