<?php

/**
 * Files sharing implementation
 *
 * @package activeCollab.modules.files
 * @subpackage models
 */
class IFileSharingImplementation extends IProjectAssetSharingImplementation {

  /**
   * Return sharing context
   *
   * @return string
   */
  function getSharingContext() {
    return ProjectAssets::FILES_SHARING_CONTEXT;
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
   * Return prepared shared body
   *
   * @param string $interface
   * @return string
   */
  function getSharedBody($interface = AngieApplication::INTERFACE_DEFAULT) {
  	
  	// Default web interface
  	if($interface == AngieApplication::INTERFACE_DEFAULT) {
  		$result = '<div class="shared_file">';
	    $result.=   '<div class="real_preview">';
	    $result.=     $this->object->preview()->renderLarge();
	    $result.=   '</div>';
	    $result.=   '<div class="object_body_content formatted_content">';
	    if ($this->object->getBody()) {
	      $result .= HTML::toRichText($this->object->getBody());
	    } // if
	    $result.=   '</div>';
	    $result.=   '<div class="download_file"><a href="' . $this->getSharedDownloadUrl() . '" target="_blank">' . lang('Download') . '</a></div>';
	    $result.= '</div>';
  	
  	// Phone interface
	  } elseif($interface == AngieApplication::INTERFACE_PHONE) {
	  	$result = '<div class="shared_file">';
	  	$result.= 	'<div class="object_content">';
	    $result.= 		$this->object->preview()->renderLarge();
	    if($this->object->getBody()) {
	    	$result.=   '<div class="wireframe_content_wrapper">';
	    	$result.=   	'<div class="object_body_content">';
	      $result.= 			HTML::toRichText($this->object->getBody());
	      $result.=   	'</div>';
	    	$result.=   '</div>';
	    } // if
	    $result.=   	'<div class="download_file"><a href="' . $this->getSharedDownloadUrl() . '" data-role="button" data-theme="k" target="_blank">' . lang('Download') . '</a></div>';
	    $result.= 	'</div>';
	    $result.= '</div>';
	  } // if
    
    return $result;
  } // getSharedBody

  /**
   * Get the shared download url
   *
   * @return string
   */
  function getSharedDownloadUrl() {
    if($this->getSharingProfile() instanceof SharedObjectProfile) {
      return Router::assemble('shared_file_download', array(
        'sharing_code' => $this->getSharingProfile()->getSharingCode(),
      ));
    } else {
      throw new InvalidInstanceError('sharing_profile', $this->getSharingProfile(), 'SharedObjectProfile');
    } // if
  } // getSharedDownloadUrl

}