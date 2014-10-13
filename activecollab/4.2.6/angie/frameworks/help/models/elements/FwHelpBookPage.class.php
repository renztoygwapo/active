<?php

  /**
   * Framework level help book page class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class FwHelpBookPage extends HelpElement {

    /**
     * Parent book name
     *
     * @var string
     */
    private $book_name;

    /**
     * Construct and load help element
     *
     * @param string $module
     * @param HelpBook|string $book
     * @param string $path
     * @param bool $load
     */
    function __construct($module, $book, $path, $load = true) {
      $this->book_name = $book instanceof HelpBook ? $book->getShortName() : $book;

      parent::__construct($module, $path, $load);
    } // __construct

    /**
     * Return book name
     *
     * @return string
     */
    function getBookName() {
      return $this->book_name;
    } // getBookName

    /**
     * Return book's short name
     *
     * @return string
     */
    function getShortName() {
      return $this->getSlug();
    } // getShortName

    /**
     * Cached title
     *
     * @var string
     */
    protected $title;

    /**
     * Return page title
     *
     * @return string
     */
    function getTitle() {
      if($this->title === null) {
        $title = $this->getProperty('title');

        if(empty($title)) {
          $basename = basename($this->path);

          $first_dot = strpos($basename, '.');
          $second_dot = strpos($basename, '.', $first_dot + 1);

          $this->title = trim(substr($basename, $first_dot + 1, $second_dot - $first_dot - 1));
        } else {
          $this->title = $title;
        } // if
      } // if

      return $this->title;
    } // getTitle

    /**
     * Cached slug value
     *
     * @var string
     */
    protected $slug;

    /**
     * Return page slug
     *
     * @return string
     */
    function getSlug() {
      if($this->slug === null) {
        $slug = $this->getProperty('slug');

        if(empty($slug)) {
          $this->slug = Inflector::slug($this->getTitle());
        } else {
          $this->slug = $slug;
        } // if
      } // if

      return $this->slug;
    } // getSlug

  }