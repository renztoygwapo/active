<?php

  /**
   * What's new article element
   *
   * @package angie.frameworks.help
   * @subpackage models
   */
  abstract class FwHelpWhatsNewArticle extends HelpElement {

    /**
     * Application version number
     *
     * @var string
     */
    private $version_number;

    /**
     * Construct and load help element
     *
     * @param string $module
     * @param string $version_number
     * @param string $path
     * @param bool $load
     */
    function __construct($module, $version_number, $path, $load = true) {
      $this->version_number = $version_number;

      parent::__construct($module, $path, $load);
    } // __construct

    /**
     * Return book's short name
     *
     * @return string
     */
    function getShortName() {
      return $this->getSlug();
    } // getShortName

    /**
     * Return in which version change was introduced
     *
     * @return string
     */
    function getVersionNumber() {
      return $this->version_number;
    } // getVersionNumber

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
        $this->slug = ''; // str_replace('.', '-', $this->version_number) . '-';

        $slug = $this->getProperty('slug');

        if(empty($slug)) {
          $this->slug .= Inflector::slug($this->getTitle());
        } else {
          $this->slug .= $slug;
        } // if
      } // if

      return $this->slug;
    } // getSlug

  }