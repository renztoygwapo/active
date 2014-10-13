<?php

/**
 * Sharing implementation for notebooks
 *
 * @package activeCollab.modules.notebooks
 * @subpackage models
 */
class INotebookSharingImplementation extends ISharingImplementation {

  /**
   * Return sharing context
   *
   * @return string
   */
  function getSharingContext() {
    return Notebooks::SHARING_CONTEXT;
  } // getSharingContext

  /**
   * Returns true if this implementation has body text to display
   *
   * @return boolean
   */
  function hasSharedBody() {
    return true;
  } // hasSharedBody

  /**
   * Notebooks support comments
   *
   * @return bool
   */
  function supportsComments() {
    return true;
  } // supportsComments

  /**
   * Return prepared shared body
   *
   * @param string $interface
   * @return string
   */
  function getSharedBody($interface = AngieApplication::INTERFACE_DEFAULT) {
    $result = '';
  	
  	// Default web interface
  	if($interface == AngieApplication::INTERFACE_DEFAULT) {
  		$result = '<div class="shared_notebook_wrapper">';
	    $result.= '<div class="shared_notebook_page_tree">';
	    $result.= '<h2><a href="' . $this->getUrl() . '">' . clean($this->object->getName()) . '</a></h2>';
	    $result.= $this->renderSubpages($this->object);
	    $result.= '</div>';
	
	    $result.= '<div class="shared_notebook">';
	    $result.= '<h2 class="main_title">' . clean($this->object->getName()) . '</h2>';
	    if ($this->object->getBody()) {
	      $result.= HTML::toRichText($this->object->getBody());
	    } else {
	      $result.= '<p class="empty_page">' . lang('Content not provided') . '</p>';
	    } // if
	    $result.= '</div>';
	    $result.= '</div>';
  	
  	// Phone interface
	  } elseif($interface == AngieApplication::INTERFACE_PHONE) {
	  	$result = '<div class="shared_notebook">';
	  	$result.= 	'<div class="object_content">';
	    $result.=   	'<div class="wireframe_content_wrapper">';
	    $result.=   		'<div class="object_body_content">';
	    if($this->object->getBody()) {
	      $result.= HTML::toRichText($this->object->getBody());
	    } else {
	      $result.= lang('Content not provided');
	    } // if
	    $result.=   		'</div>';
	    $result.=   	'</div>';
	    $result.= 	'</div>';
	  	
	  	$result.= 	'<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">';
	  	$result.= 		'<li data-role="list-divider"><img src="assets/images/notebooks/phone/icons/listviews/navigate-pages.png" class="divider_icon" alt="">' . lang('Pages') . '</li>';
	    $result.= 			$this->renderSubpages($this->object, $interface);
	    $result.= 	'</ul>';
	    $result.= '</div>';
	  } // if

    return $result;
  } // getSharedBody

  /**
   * Render links to subpages
   * 
   * @param ISharing $object
   * @param string $interface
   * @param string $indent
   * @return string
   */
  function renderSubpages($object, $interface = AngieApplication::INTERFACE_DEFAULT, $indent = '') {
    if ($object instanceof Notebook) {
      $subpages = NotebookPages::findByNotebook($object);
    } else if ($object instanceof NotebookPage) {
      $subpages = NotebookPages::findSubpages($object);
    } else {
      $object = $this->object;
      $subpages = NotebookPages::findByNotebook($object);
    } // if

    if(!is_foreachable($subpages)) {
      return null;
    } // if
    
    // Default web interface
  	if($interface == AngieApplication::INTERFACE_DEFAULT) {
  		$result = '<ol>';
	
	    foreach ($subpages as $subpage) {
	      $result .= '<li><a href="' . $this->getPageUrl($subpage) . '">' . clean($subpage->getName()) . '</a>';
	      $result .= $this->renderSubpages($subpage);
	      $result .= '</li>';
	    } // foreach
	
	    $result.= '</ol>';
  	
  	// Phone interface
	  } elseif($interface == AngieApplication::INTERFACE_PHONE) {
	  	foreach($subpages as $subpage) {
	      $result = '<li><a href="' . $this->getPageUrl($subpage) . '">' . $indent . ' ' . clean($subpage->getName()) . '</a>';
	      $result .= $this->renderSubpages($subpage, $interface, $indent . '&middot;&middot;');
	      $result .= '</li>';
	    } // foreach
	  } // if

    return $result;
  } // renderSubpages

  /**
   * Return shared object URL
   *
   * @return string
   * @throws InvalidInstanceError
   */
  function getUrl() {
    if($this->getSharingProfile() instanceof SharedObjectProfile) {
      return Router::assemble('shared_notebook', array(
        'sharing_code' => $this->getSharingProfile()->getSharingCode(),
      ));
    } else {
      throw new InvalidInstanceError('sharing_profile', $this->getSharingProfile(), 'SharedObjectProfile');
    } // if
  } // getUrl

  /**
   * Return URL proposal, based on $code value
   *
   * @param string $code
   * @return string
   */
  function getUrlProposal($code) {
    return Router::assemble('shared_notebook', array(
      'sharing_code' => $code,
    ));
  } // getUrlProposal

  /**
   * Returns the URL for shared page
   *
   * @param NotebookPage $page
   * @return string
   * @throws InvalidInstanceError
   */
  function getPageUrl(NotebookPage $page) {
    if($this->getSharingProfile() instanceof SharedObjectProfile) {
      return Router::assemble('shared_notebook_page', array(
        'sharing_code' => $this->getSharingProfile()->getSharingCode(),
        'notebook_page_id' => $page->getId()
      ));
    } else {
      throw new InvalidInstanceError('sharing_profile', $this->getSharingProfile(), 'SharedObjectProfile');
    } // if
  } // getPageUrl

}