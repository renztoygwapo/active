<?php

  /**
   * Pager class
   * 
   * Every instance is used to describe a state of paginated result - number of 
   * total pages, current page and how many projects are listed per page
   */
  class Pager {
    
    /**
     * Total number of items that will be shown on pages
     *
     * @var integer
     */
    private $total_items = 0;
    
    /**
     * Number of items per page
     *
     * @var integer
     */
    private $items_per_page = 10;
    
    /**
     * Current page NUM
     *
     * @var integer
     */
    private $current_page = 1;
    
    /**
     * Cached last page value. If null the value will be calculated by the
     * getLastPage() function and saved
     *
     * @var integer
     */
    private $last_page = null;
    
    /**
     * Template that is used for links when pagination is rendered
     *
     * @var string
     */
    var $link_template = null;
    
    /**
     * Construct pager object
     * 
     * @param integer $total_items
     * @param integer $current_page
     * @param integer $per_page
     * @param string $link_template
     * @return Pager
     */
    function __construct($total_items, $current_page, $per_page, $link_template = null) {
      $this->current_page = $current_page;
      $this->total_items = $total_items;
      $this->items_per_page = $per_page;
      $this->link_template = $link_template;
    } // __construct
    
    // ---------------------------------------------------
    //  Logic
    // ---------------------------------------------------
    
    /**
     * Check if specific page is first page. If $page is null function will use
     * current page
     *
     * @param integer $page Page that need to be checked. If null function will
     *   use current page
     * @return boolean
     */
    function isFirst($page = null) {
      $page = is_null($page) ? $this->getCurrentPage() : (integer) $page;
      return $page == 1;
    } // isFirst
    
    /**
     * Check if specific page is last page. If $page is null function will use
     * current page
     *
     * @param integer $page Page that need to be checked. If null function will
     *   use current page
     * @return boolean
     */
    function isLast($page = null) {
      $page = is_null($page) ? $this->getCurrentPage() : (integer) $page;
      if(is_null($last = $this->getLastPage())) {
        return false;
      } // if
      return $page == $last;
    } // isLast
    
    /**
     * Check if specific page has next page. If $page is null function will use
     * current page
     *
     * @param integer $page Page that need to be checked. If null function will
     *   use current page
     * @return boolean
     */
    function hasNext($page = null) {
      $page = is_null($page) ? $this->getCurrentPage() : (integer) $page;
      if(is_null($last = $this->getLastPage())) {
        return false;
      } // if
      return $page < $last;
    } // hasNext
    
    /**
     * Return num of last page
     *
     * @param void
     * @return integer
     */
    function getLastPage() {
      if($this->last_page === null) {
        if(($this->getItemsPerPage() < 1) || ($this->getTotalItems() < 1)) {
          $this->last_page = 1;
        } else {
          if(($this->getTotalItems() % $this->getItemsPerPage()) == 0) {
            $this->last_page = (integer) ($this->getTotalItems() / $this->getItemsPerPage());
          } else {
            $this->last_page = (integer) ($this->getTotalItems() / $this->getItemsPerPage()) + 1; 
          } // if
        } // if
      } // if
      
      return $this->last_page;
    } // getLastPage
    
    /**
     * Returns num of last page... If there is no last page (we are on it) return
     * NULL
     *
     * @param void
     * @return integer
     */
    function getNextPage() {
      return $this->current_page < $this->getLastPage() ? $this->current_page + 1 : $this->getLastPage();
    } // getNextPage
    
    /**
     * Render pagination HTML
     *
     * @param boolean $skip_if_empty
     * @return string
     */
    function render($skip_if_empty = false) {
    	if($this->total_items < 1) {
    		$total_pages = 1;
    	} else {
    		$total_pages = ceil($this->total_items / $this->items_per_page);
    	} // if
    	
    	// Skip if empty
    	if($this->total_items < 2 && $skip_if_empty) {
    	  return '';
    	} // if
    	
    	$urls = array();
    
    	// Prepare sorounding pages numbers (3 before current page and three after)
    	$sourounding = array();
    	$start_range = $this->current_page - 2;
    	if($start_range < 1) {
    		$start_range = 1;
    	} // if
    	$end_range = $this->current_page + 2;
    	if($end_range > $total_pages) {
    		$end_range = $total_pages;
    	} // if
    
    	for($i = $start_range; $i <= $end_range; $i++) {
    		$sourounding[] = $i;
    	} // for
    
    	// Render content into an array
    	$before_dots_rendered = false;
    	$after_dots_rendered = false;
    
    	if($this->current_page > 1 && $this->current_page !== false) {
    		$urls[] = '<li class="prev"><a href="' . str_replace(PAGE_PLACEHOLDER, $this->current_page-1, $this->link_template) . '" title="' . lang('Previous Page') . '"><span class="laquo">&laquo; </span>' . lang('Prev.') . '</a></li>';
    	} else {
    	  $urls[] = '<li class="prev false"><span title="' . lang('Previous Page') . '" class="disabled">&laquo; ' . lang('Prev.') . '</span></li>';
    	} // if
    
    	for($i = 1; $i <= $total_pages; $i++) {
    		// Print page...
    		if(($i == 1) || ($i == $total_pages) || in_array($i, $sourounding)) {
    		  $class_name='';
    		  if ($i == 1) {
    		    $class_name = 'first';
    		  } else if ($i == $total_pages) {
    		    $class_name = 'last';
    		  } // if
    		  
    			if($this->current_page == $i) {
    				$urls[] = '<li class="' . $class_name . '"><span class="current"><strong>' . $i . '</strong></span></li>';
    			} else {
    				$urls[] = '<li class="' . $class_name . '"><a href="' . str_replace(PAGE_PLACEHOLDER, $i, $this->link_template) . '">' . $i . '</a></li>';
    			} // if
     
    			// Print dots if they are not rendered
    		} else {
    			if($i < $this->current_page && !$before_dots_rendered) {
    				$before_dots_rendered = true;
    				$urls[] = '<li><span class="dots">...</span></li>';
    			} elseif($i > $this->current_page && !$after_dots_rendered) {
    				$after_dots_rendered = true;
    				$urls[] = '<li><span class="dots">...</span></li>';
    			} // if
    		} // if
    
    	} // for
    
    	if($this->current_page < $total_pages && $this->current_page !== false) {
    		$urls[] = '<li class="next"><a href="' . str_replace(PAGE_PLACEHOLDER, $this->current_page + 1, $this->link_template) . '" title="' . lang('Next Page') . '">' . lang('Next') . ' &raquo;</a></li>';
    	} else {
    	  $urls[] = '<li class="next false"><span title="' . lang('Next Page') . '" class="disabled">' . lang('Next') . '<span class="raquo"> &raquo;</span></span></li>';
    	} // if
    	
    	return '<div class="pagination"><ul>' . implode('', $urls) . '</ul></div>';
    } // render
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return total items value
     *
     * @param void
     * @return integer
     */
    function getTotalItems() {
      return $this->total_items;
    } // getTotalItems
    
    /**
     * Return items per page value
     *
     * @param void
     * @return integer
     */
    function getItemsPerPage() {
      return $this->items_per_page;
    } // getItemsPerPage
    
    /**
     * Return current page value
     *
     * @param void
     * @return integer
     */
    function getCurrentPage() {
      return $this->current_page;
    } // getCurrentPage
    
    /**
     * Set link template value
     *
     * @param string $value
     */
    function setLinkTemplate($value) {
      $this->link_template = $value;
    } // setLinkTemplate
    
  }

?>