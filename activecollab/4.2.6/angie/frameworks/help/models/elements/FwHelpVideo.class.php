<?php

  /**
   * Framework level help video class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class FwHelpVideo extends HelpElement {

    /**
     * Return book's short name
     *
     * @return string
     */
    function getShortName() {
      return $this->getSlug();
    } // getShortName

    /**
     * Cached group name value
     *
     * @var string
     */
    private $group_name = false;

    /**
     * Return name of the group that this video belongs to
     *
     * @return string
     */
    function getGroupName() {
      if($this->group_name === false) {
        $this->group_name = $this->getProperty('group');

        if(empty($this->group_name)) {
          $this->group_name = AngieHelpDelegate::GETTING_STARTED_VIDEO_GROUP;
        } // if
      } // if

      return $this->group_name;
    } // getGroupName

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

    /**
     * Return property description
     *
     * @return string
     */
    function getDescription() {
      return $this->getProperty('description');
    } // getDescription

    /**
     * Return source URL
     *
     * Only supported modifier at the moment is 2X
     *
     * @param string $modifier
     * @return string
     */
    function getSourceUrl($modifier = null) {
      if(empty($modifier)) {
        return $this->getProperty('url');
      } else {
        return $this->getProperty('url' . strtolower($modifier));
      } // if
    } // getSourceUrl

    /**
     * Cached play time value
     *
     * @var string
     */
    private $play_time = false;

    /**
     * Return video play time
     *
     * @return string
     */
    function getPlayTime() {
      if($this->play_time === false) {
        $this->play_time = $this->getProperty('play_time');

        if(empty($this->play_time)) {
          $this->play_time = lang('-:--');
        } // if
      } // if

      return $this->play_time;
    } // getPlayTime

  }