<?php

  /**
   * Help element search item implementation
   * 
   * @package angie.frameworks.help
   * @subpackage models
   */
  abstract class FwIHelpElementSearchItemImplementation extends ISearchItemImplementation {
  
    /**
     * Return list of indices that index parent object
     * 
     * Result is an array where key is the index name, while value is list of 
     * fields that's watched for changes
     * 
     * @return array
     */
    function getIndices() {
      return array(
        'help' => array('title', 'body'),
      );
    } // getIndices

    /**
     * Get additional search index
     *
     * @param SearchIndex $index
     * @return mixed|void
     * @throws InvalidInstanceError
     */
    function getAdditional(SearchIndex $index) {
      if($index instanceof HelpSearchIndex) {
        return array(
          'title' => $this->object->getTitle(),
          'body' => $this->object->getBody(),
        );
      } else {
        throw new InvalidInstanceError('index', $index, 'HelpSearchIndex');
      } // if
    } // getAdditional

    // ---------------------------------------------------
    //  Describe
    // ---------------------------------------------------

    /**
     * Describe for search
     *
     * @param IUser $user
     * @return array
     */
    function describeForSearch(IUser $user) {
      if($this->object instanceof HelpBook) {
        $verbose_type = lang('Book');
      } elseif($this->object instanceof HelpBookPage) {
        $verbose_type = lang('Page');
      } elseif($this->object instanceof HelpWhatsNewArticle) {
        $verbose_type = lang('Article');
      } elseif($this->object instanceof HelpVideo) {
        $verbose_type = lang('Video');
      } else {
        $verbose_type = lang('Help');
      } // if

      return array(
        'id' => $this->object->getShortName(),
        'type' => get_class($this->object),
        'verbose_type' => $verbose_type,
        'name' => $this->object->getTitle(),
        'permalink' => $this->object->getUrl(),
        'is_crossed_over' => false,
      );
    } // describeForSearch

  }