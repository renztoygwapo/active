<?php

  // Build on top of the application level controller
  AngieApplication::useController('help', HELP_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level help books controller
   *
   * @package angie.frameworks.help
   * @subpackage controllers
   */
  abstract class FwHelpBooksController extends HelpController {

    /**
     * Execute before other actions
     */
    function __before() {
      parent::__before();

      $this->wireframe->breadcrumbs->add('books', lang('Books'), Router::assemble('help_books'));
      $this->wireframe->tabs->setCurrentTab('books');
    } // __before

    /**
     * List all books
     */
    function index() {
      $this->response->assign(array(
        'books' => AngieApplication::help()->getBooks($this->logged_user),
      ));
    } // index

    /**
     * Show a particular book
     */
    function book() {
      $book_name = $this->request->get('book_name');

      $book = $book_name ? AngieApplication::help()->getBooks()->get($book_name) : null;

      if($book instanceof HelpBook) {
        if($book->canView($this->logged_user)) {
          $this->wireframe->print->enable();
          $this->response->assign('book', $book);
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // book

    /**
     * Show single book page
     */
    function page() {
      $this->setView('book');

      $book_name = $this->request->get('book_name');
      $page_name = $this->request->get('page_name');

      $book = $book_name ? AngieApplication::help()->getBooks()->get($book_name) : null;

      if($book instanceof HelpBook) {
        if($book->canView($this->logged_user)) {
          $page = $page_name ? $book->getPages($this->logged_user)->get($page_name) : null;

          if($page instanceof HelpBookPage) {
            if($page->canView($this->logged_user)) {
              $this->wireframe->print->enable();

              $this->response->assign(array(
                'book' => $book,
                'selected_page' => $page->getShortName(),
              ));
            } else {
              $this->response->forbidden();
            } // if
          } else {
            $this->response->notFound();
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // page

  }