<?php

  /**
   * Test router
   */
  class TestRouter extends UnitTestCase {

    /**
     * Set up test enviornment
     */
    function setUp() {
      parent::setUp();

      Router::cleanUp();
      Router::cleanUpCache(true);
    } // setUp

    /**
     * Test simple route match
     */
    function testSimpleMatch() {
      Router::map('test_route', 'test', array('controller' => 'test'));

      $routes = Router::getRoutes();

      $this->assertTrue(is_array($routes) && count($routes) == 1);
      $this->assertIsA($routes['test_route'], 'Route');

      Router::recompile();

      $request = Router::match('test', '');

      $this->assertIsA($request, 'Request');
      $this->assertEqual($request->getMatchedRoute(), 'test_route');
    } // testTwoSlugs

    /**
     * Test match with multiple slugs
     */
    function testMatchWithTwoSlugs() {
      $help_book_regexp = '/^help\\/([a-z0-9\\-\\._]+)$/';
      $help_book_page_regexp = '/^help\\/([a-z0-9\\-\\._]+)\\/([a-z0-9\\-\\._]+)$/';

      Router::map('help_book', 'help/:book_name', array('controller' => 'test'));
      Router::map('help_book_page', 'help/:book_name/:page_name', array('controller' => 'test'));

      $routes = Router::getRoutes();

      $this->assertTrue(is_array($routes) && count($routes) == 2);

      if($routes['help_book'] instanceof Route) {
        $this->assertEqual($routes['help_book']->getRegularExpression(), $help_book_regexp);

        $matches = null;
        $this->assertEqual(preg_match($routes['help_book']->getRegularExpression(), 'help/projects', $matches), 1);

        $this->assertEqual($matches, array('help/projects', 'projects'));
      } else {
        $this->fail('Route "help_book" not defined');
      } // if

      if($routes['help_book_page'] instanceof Route) {
        $this->assertEqual($routes['help_book_page']->getRegularExpression(), $help_book_page_regexp);

        $matches = null;
        $this->assertEqual(preg_match($routes['help_book_page']->getRegularExpression(), 'help/projects/development-123-something', $matches), 1);

        $this->assertEqual($matches, array('help/projects/development-123-something', 'projects', 'development-123-something'));
      } else {
        $this->fail('Route "help_book_page" not defined');
      } // if

      Router::recompile();

      $request = Router::match('help/projects', '', false);

      $this->assertIsA($request, 'Request');
      $this->assertEqual($request->getMatchedRoute(), 'help_book');

      $url_params = $request->getUrlParams();

      $this->assertEqual($url_params['book_name'], 'projects');

      $request = Router::match('help/projects/create-a-project', '');

      $this->assertIsA($request, 'Request');
      $this->assertEqual($request->getMatchedRoute(), 'help_book_page');

      $url_params = $request->getUrlParams();

      $this->assertEqual($url_params['book_name'], 'projects');
      $this->assertEqual($url_params['page_name'], 'create-a-project');
    } // testMatchWithTwoSlugs

  }