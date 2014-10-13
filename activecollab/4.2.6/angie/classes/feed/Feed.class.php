<?php

  /**
   * Feed class
   * 
   * @package angie.library.feed
   */
  class Feed {
    
    /**
     * Feed title
     *
     * @var string
     */
    var $title;
    
    /**
     * Website URL
     *
     * @var string
     */
    var $link;
    
    /**
     * Feed description
     *
     * @var string
     */
    var $description;
    
    /**
     * Language used in feed
     *
     * @var string
     */
    var $language;
    
    /**
     * Feed author
     *
     * @var FeedAuthor
     */
    var $author;
    
    /**
     * Array of feed items
     *
     * @var array
     */
    var $items = array();
  
    /**
     * Constructor
     * 
     * Construct the feed object and set feed properties
     *
     * @param string $title
     * @param string $link
     * @param string $description
     * @param string $language
     * @param FeedAuthor $author
     * @return Feed
     */
    function __construct($title, $link, $description = null, $language = null, $author = null) {
      $this->setTitle($title);
      $this->setLink($link);
      $this->setDescription($description);
      $this->setLanguage($language);
      $this->setAuthor($author);
    } // __construct
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get title
     *
     * @return string
     */
    function getTitle() {
      return $this->title;
    } // getTitle
    
    /**
     * Set title value
     *
     * @param string $value
     */
    function setTitle($value) {
      $this->title = $value;
    } // setTitle
    
    /**
     * Get link
     *
     * @return string
     */
    function getLink() {
      return $this->link;
    } // getLink
    
    /**
     * Set link value
     *
     * @param string $value
     */
    function setLink($value) {
      $this->link = $value;
    } // setLink
    
    /**
     * Get description
     *
     * @return string
     */
    function getDescription() {
      return $this->description;
    } // getDescription
    
    /**
     * Set description value
     *
     * @param string $value
     */
    function setDescription($value) {
      $this->description = $value;
    } // setDescription
    
    /**
     * Get language
     *
     * @return string
     */
    function getLanguage() {
      return $this->language;
    } // getLanguage
    
    /**
     * Set language value
     *
     * @param string $value
     */
    function setLanguage($value) {
      $this->language = $value;
    } // setLanguage
    
    /**
     * Get author
     *
     * @return FeedAuthor
     */
    function getAuthor() {
      return $this->author;
    } // getAuthor
    
    /**
     * Set author value
     *
     * @param FeedAuthor $value
     */
    function setAuthor($value) {
      $this->author = $value;
    } // setAuthor
    
    /**
     * Return an array of feed items
     *
     * @return array
     */
    function getItems() {
      return $this->items;
    } // getItems
    
    /**
     * Add item to feed
     * 
     * This function will add single feed item to the feed and return the item that was added
     *
     * @param FeedItem $item
     * @return FeedItem
     */
    function addItem($item) {
      $this->items[] = $item;
      return $item;
    } // addItem
  
  } // Feed

?>