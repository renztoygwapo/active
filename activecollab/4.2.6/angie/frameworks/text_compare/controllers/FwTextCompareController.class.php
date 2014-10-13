<?php

  // Build on top of backend controller
  AngieApplication::useController('frontend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Text compare controller implementation
   *
   * @package angie.frameworks.text_compare
   * @subpackage controllers
   */
  abstract class FwTextCompareController extends FrontendController {
    
    /**
     * Compare text
     */
    function compare_text() {
    	// Load final version
      $final_version = $this->request->post('final');
      
      // Load version to compare with
      $compare_with_version = $this->request->post('compare_with');
      
      $name_diff = render_diff($compare_with_version['name'], $final_version['name']);
      if(empty($name_diff)) {
        $name_diff = $compare_with_version['name'];
      } // if
      
      $original = HTML::toPlainText($compare_with_version['body']);
      
      $body_diff = render_diff($original, HTML::toPlainText($final_version['body']));
      if(empty($body_diff)) {
        $body_diff = $original;
      } // if
      
      // Display
      $this->smarty->assign(array(
      	'final_version_label' => $final_version['version'],
        'compare_with_version_label' => $compare_with_version['version'],
        'final_version' => $final_version,
        'compare_with_version' => $compare_with_version,
        'name_diff' => $name_diff,
        'body_diff' => $body_diff,
      ));
    } // compare_text
    
    /**
     * Compare versions
     */
    function compare_versions() {
    	// Load all versions
    	$versions = $this->request->post('versions');
    	
    	// Load left version
      $left_version_number = $this->request->post('left');
      if(is_foreachable($versions)) {
      	foreach($versions as $version) {
      		if($version['version'] == $left_version_number) {
      			$left_version = $version;
      			$left_version_label = $left_version['version'];
      		} // if
      	} // foreach
      } // if

      // Load right version
      $right_version_number = $this->request->post('right');
      if(is_foreachable($versions)) {
      	foreach($versions as $version) {
      		if($version['version'] == $right_version_number) {
      			$right_version = $version;
      			$right_version_label = $right_version['version'];
      		} // if
      	} // foreach
      } // if

      $name_diff = render_diff($right_version['name'], $left_version['name']);
      if(empty($name_diff)) {
        $name_diff = $right_version['name'];
      } // if
      
      $original = HTML::toPlaintext($right_version['body']);
      
      $body_diff = render_diff($original, HTML::toPlaintext($left_version['body']));
      if(empty($body_diff)) {
        $body_diff = $original;
      } // if
      
      // Display
      $this->smarty->assign(array(
      	'left_version_label' => $left_version_label,
        'right_version_label' => $right_version_label,
        'left_version' => $left_version,
        'right_version' => $right_version,
        'name_diff' => $name_diff,
        'body_diff' => $body_diff,
        'versions' => $versions
      ));
    } // compare_versions
    
  }