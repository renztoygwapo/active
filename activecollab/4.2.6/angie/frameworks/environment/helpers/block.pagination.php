<?php

  /**
   * Show page pagination
   * 
   * Parameters:
   * 
   * - pager - Pagination object
   * - page - Current page, used when Pagination description is not provided
   * - per_page - Number of items listed per page, used when Pagination 
   *   description is not provided
   * - total - Total number of pages, used when Pagination description is not 
   *   provided
   * - title - label that should precede pagination
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_block_pagination($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
  
  	// ---------------------------------------------------
  	//  Collect parameters
  	// ---------------------------------------------------
  
  	if(isset($params['pager'])) {
  		$pager = array_var($params, 'pager');
  		if($pager instanceof Pager) {
  			$current_page = $pager->getCurrentPage();
  			$total_items = $pager->getTotalItems();
  			$items_per_page = $pager->getItemsPerPage();
  		} else {
  			return new InvalidParamError('pager', $pager, "'pager' is expected to be an instance of Pager class", true);
  		} // if
  	} else {
  		$current_page = array_var($params, 'page');
  		$items_per_page = array_var($params, 'per_page');
  		$total_items = array_var($params, 'total');
  	} // if
  
  	if($current_page === null) {
  		return new InvalidParamError('page', $current_page, "'page' property is required for 'pagination' helper", true);
  	} // if
  
  	if($items_per_page === null) {
  		return new InvalidParamError('per_page', $items_per_page, "'per_page' property is required for 'pagination' helper", true);
  	} // if
  
  	if($total_items === null) {
  		return new InvalidParamError('total', $total_items, "'total' property is required for 'pagination' helper", true);
  	} // if
  	
  	$title = array_var($params, 'title', lang('Page'));
  	  
  	$page_placeholder = array_var($params, 'placeholder', '-PAGE-');
  
  	$url_base = $content;
  
  	// ---------------------------------------------------
  	//  Now render pagination
  	// ---------------------------------------------------
  
  	if($total_items < 1) {
  		$total_pages = 1;
  	} else {
  		$total_pages = ceil($total_items / $items_per_page);
  	} // if
  	
  	// If sensitive, don't render unless necessary
  	if((array_var($params, 'sensitive', false) == true) && ($total_pages < 2)) {
  	  return false;
  	} // if
  	
  	$urls = array();
  
  	// Prepare sorounding pages numbers (3 before current page and three after)
  	$sourounding = array();
  	$start_range = $current_page - 2;
  	if($start_range < 1) {
  		$start_range = 1;
  	} // if
  	$end_range = $current_page + 2;
  	if($end_range > $total_pages) {
  		$end_range = $total_pages;
  	} // if
  
  	for($i = $start_range; $i <= $end_range; $i++) {
  		$sourounding[] = $i;
  	} // for
  
  	// Render content into an array
  	$before_dots_rendered = false;
  	$after_dots_rendered = false;
  
  	if($current_page > 1 && $current_page !== false) {
  		$urls[] = '<li class="prev"><a href="' . str_replace($page_placeholder, $current_page-1, $url_base) . '" title="' . lang('Previous Page') . '"><span class="laquo">&laquo; </span>' . lang('Prev.') . '</a></li>';
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
  		  
  			if($current_page == $i) {
  				$urls[] = '<li class="' . $class_name . '"><span class="current"><strong>' . $i . '</strong></span></li>';
  			} else {
  				$urls[] = '<li class="' . $class_name . '"><a href="' . str_replace($page_placeholder, $i, $url_base) . '">' . $i . '</a></li>';
  			} // if
   
  			// Print dots if they are not rendered
  		} else {
  			if($i < $current_page && !$before_dots_rendered) {
  				$before_dots_rendered = true;
  				$urls[] = '<li><span class="dots">...</span></li>';
  			} elseif($i > $current_page && !$after_dots_rendered) {
  				$after_dots_rendered = true;
  				$urls[] = '<li><span class="dots">...</span></li>';
  			} // if
  		} // if
  
  	} // for
  
  	if($current_page < $total_pages && $current_page !== false) {
  		$urls[] = '<li class="next"><a href="' . str_replace($page_placeholder, $current_page+1, $url_base) . '" title="' . lang('Next Page') . '">' . lang('Next') . ' &raquo;</a></li>';
  	} else {
  	  $urls[] = '<li class="next false"><span title="' . lang('Next Page') . '" class="disabled">' . lang('Next') . '<span class="raquo"> &raquo;</span></span></li>';
  	} // if
  
  	
  	$output_pagination = '<div class="pagination">';
  	if ($title) {
  	  $output_pagination.= '<span class="title">' . $title . ':</span>';
  	} // if
  	$output_pagination.= '<ul>';
  	$output_pagination.= implode('', $urls);
  	$output_pagination.= '</ul>';
  	$output_pagination.= '</div>';
  	
  	return $output_pagination;
  } // smarty_function_pagination

?>