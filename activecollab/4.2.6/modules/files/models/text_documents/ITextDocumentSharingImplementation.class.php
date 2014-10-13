<?php

/**
 * Text documents sharing implementation
 *
 * @package activeCollab.modules.files
 * @subpackage models
 */
class ITextDocumentSharingImplementation extends IProjectAssetSharingImplementation {

  /**
   * Return sharing context
   *
   * @return string
   */
  function getSharingContext() {
    return ProjectAssets::DOCUMENTS_SHARING_CONTEXT;
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
  		$result = '<div class="shared_text_document">';
	    $result.= HTML::toRichText($this->object->getBody());
	    $result.= '</div>';
  	
  	// Phone interface
	  } elseif($interface == AngieApplication::INTERFACE_PHONE) {
	  	$result = '<div class="shared_text_document">';
	  	$result.= 	'<div class="object_content">';
	    $result.=   	'<div class="wireframe_content_wrapper">';
	    $result.=   		'<div class="object_body_content">';
	    $result.= 				HTML::toRichText($this->object->getBody());
	    $result.=   		'</div>';
	    $result.=   	'</div>';
	    $result.= 	'</div>';
	    $result.= '</div>';
	  } // if

    return $result;
  } // getSharedBody

}