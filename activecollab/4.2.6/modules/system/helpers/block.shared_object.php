<?php

  /**
   * Shared object inspector implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render shared object details
   * 
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_shared_object($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    $object = array_required_var($params, 'object', true, 'ISharing');
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    // Default web interface
	  if($interface == AngieApplication::INTERFACE_DEFAULT) {
	  	$result = '<div class="shared_object_inspector"><ul>';
	    
	    foreach($object->sharing()->getSharedProperties() as $k => $v) {
	      $result .= '<li>' . clean($v['label']) . ': ' . (isset($v['html']) ? $v['html'] : clean($v['text'])) . '</li>'; 
	    } // foreach
	    
	    $result .= '</ul>';
	  
	  // Phone interface
	  } elseif($interface == AngieApplication::INTERFACE_PHONE) {
	  	$result = '<div>';
	  } // if
	  
	  if($object->sharing()->hasSharedBody()) {
      $result .= '<div class="body">' . $object->sharing()->getSharedBody($interface) . '</div>';
    } // if
	  
	  if(trim($content)) {
      $result .= $content;
    } // if
	  
	  return $result . '</div>';
  } // smarty_block_shared_object