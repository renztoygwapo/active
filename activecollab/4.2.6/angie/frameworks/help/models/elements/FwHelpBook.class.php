<?php

  /**
   * Framework level help book implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class FwHelpBook extends HelpElement {

    /**
     * Return book title
     *
     * @return string
     */
    function getTitle() {
      return $this->getProperty('title', 'Book');
    } // getTitle

    /**
     * Return property description
     *
     * @return string
     */
    function getDescription() {
      return $this->getProperty('description');
    } // getDescription

    /**
     * Return book cover URL
     *
     * @param bool $small
     * @return string
     */
    function getCoverUrl($small = true) {
      $size_suffix = $small ? 'small' : 'large';
      $cover_image_file = "/_cover_$size_suffix.png";
      $image_relative_path = "books/" . str_replace('_', '-', $this->getFolderName()) . $cover_image_file;

      if (is_file($this->path . '/images' . $cover_image_file)) {
        return AngieApplication::getImageUrl($image_relative_path, $this->getModuleName(), 'help');
      } else {
        return AngieApplication::getImageUrl('book.png', HELP_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT);
      } // if
    } // getCoverUrl

    /**
     * Cached pages, per user
     *
     * @var array
     */
    private $pages = array();

    /**
     * Show pages that $user can see
     *
     * @param User $user
     * @return HelpBookPage[]|NamedList
     */
    function getPages(User $user = null) {
      $key = $user instanceof User ? $user->getId() : 'all';

      if(empty($this->pages[$key])) {
        $pages = new NamedList();

        $files = get_files($this->path . '/pages', 'md', false);

        if($files && is_foreachable($files)) {
          sort($files); // Make sure that files are properly sorted

          foreach($files as $file) {
            $page = new HelpBookPage($this->module, $this, $file, true);

            if($page->isLoaded()) {
              $pages->add($page->getShortName(), $page);
            } // if
          } // foreach
        } // if

        $this->pages[$key] = $pages;
      } // if

      return $this->pages[$key];
    } // getPages

    /**
     * Populate list of common questions
     *
     * @param array $common_questions
     * @param User $user
     */
    function populateCommonQuestionsList(&$common_questions, User $user = null) {
      foreach($this->getPages($user) as $page) {
        $answers_common_question = $page->getProperty('answers_common_question');

        if($answers_common_question) {
          $common_questions[] = array(
            'question' => $answers_common_question,
            'page_url' => $page->getUrl(),
            'position' => (integer) $page->getProperty('answer_position'),
          );
        } // if
      } // foreach
    } // populateCommonQuestionsList

  }