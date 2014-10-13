<?php

  /**
   * Help search index
   * 
   * @package angie.frameworks.help
   * @subpackage models
   */
  abstract class FwHelpSearchIndex extends SearchIndex {
    
    /**
     * Return short name of this index
     * 
     * @return string
     */
    function getShortName() {
      return 'help';
    } // getShortName

    /**
     * Return type of ID field
     *
     * @return string
     */
    function getIdType() {
      return SearchIndex::ID_STRING;
    } // getIdType
  
    /**
     * Return index name
     * 
     * @return string
     */
    function getName() {
      return lang('Help');
    } // getName
    
    /**
     * Return index fields
     * 
     * @return array
     */
    function getFields() {
      return array(
        'title' => self::FIELD_STRING,
        'body' => self::FIELD_TEXT,
      );
    } // getFields

    /**
     * Return item instance
     *
     * @param IUser $user
     * @param string $item_class
     * @param string $item_id
     * @return DataObject
     */
    function loadItemDetails(IUser $user, $item_class, $item_id) {
      if($item_class && $item_id && class_exists($item_class, true)) {
        $item = null;

        switch($item_class) {
          case 'HelpBookPage':
            list($book_name, $page_name) = explode('/', $item_id);

            $book = AngieApplication::help()->getBooks()->get($book_name);

            if($book instanceof HelpBook) {
              $item = $book->getPages()->get($page_name);
            } // if

            break;
          case 'HelpWhatsNewArticle':
            $item = AngieApplication::help()->getWhatsNew()->get($item_id);
            break;
          case 'HelpVideo':
            $item = AngieApplication::help()->getVideos()->get($item_id);
            break;
        } // switch

        if($item instanceof HelpElement) {
          return $item->search()->describeForSearch($user);
        } // if
      } // if

      return null;
    } // loadItemDetails
    
    // ---------------------------------------------------
    //  Rebuild
    // ---------------------------------------------------
    
    /**
     * Return steps to rebuild this search index
     */
    function getRebuildSteps() {
      $steps = parent::getRebuildSteps();
      
      $steps[] = array(
        'text' => lang('Build Index'), 
       	'url' => $this->getBuildUrl(),
      );
      
      return $steps;
    } // getRebuildSteps
    
    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------
    
    /**
     * Return build index URL
     * 
     * @return string
     */
    function getBuildUrl() {
      return Router::assemble('help_search_index_admin_build');
    } // getBuildUrl
    
  }