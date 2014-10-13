<?php

  /**
   * object_attachments helper
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * List object attachments
   * 
   * Parameters:
   * 
   * - object - selected object
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_attachments($params, &$smarty) {
    $object = array_required_var($params, 'object', true, 'IAttachments');
    $user = array_required_var($params, 'user', true, 'IUser');
    
    $interface = isset($params['interface']) ? $params['interface'] : AngieApplication::getPreferedInterface();
    
    $attachments = $object->attachments()->get($user);
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('object_attachments');
    } // if
      
    if(isset($params['class'])) {
      $params['class'] .= ' object_attachments';
    } else {
      $params['class'] = 'object_attachments';
    } // if
    
    // Regular web interface
    if($interface == AngieApplication::INTERFACE_DEFAULT) {
      AngieApplication::useWidget('jwplayer', ENVIRONMENT_FRAMEWORK);

    	$options = array(
    		'object' => array(
    			'id' => $object->getId(),
    			'class' => get_class($object),
    			'listen' => $object->getUpdatedEventName() 
    		),
    		'attachments' => array()
    	);
        
    	if($attachments) {
  			foreach($attachments as $attachment) {
  	    	$options['attachments'][] = $attachment->describe($user, array(
  	      	'detailed' => true,  
  	       ));
  	    } // foreach
    	} // if
	      
	    return HTML::openTag('div', $params) . '</div><script type="text/javascript">$("#' . $params['id'] . '").objectAttachments(' . JSON::encode($options, $user) . ')</script>';
	      
      // Phone interface
    } elseif($interface == AngieApplication::INTERFACE_PHONE) {
	    if(is_foreachable($attachments)) {
	    	$return = '<div class="object_attachments">';
	    		
	    	foreach($attachments as $attachment) {
	    		$return .= '<div class="attachment">';
	    		$return .= $attachment->preview()->renderSmall();
		     	$return .= '<span class="filename">' . clean($attachment->getName()) .'</span>';
	    		$return .= '</div>';
	      } // foreach
	      
	      return $return .= '</div>';
    	} // if
      	
    // Print interface
    } else if ($interface == AngieApplication::INTERFACE_PRINTER) {
    	if(is_foreachable($attachments)) {
	     	$return = '<div class="object_attachments"><ul class="attachments_table">';
	     	foreach ($attachments as $attachment) {
	     		$return.= '<li class="attachment"><a href="#">';
	     		$return.= '<img src="' . $attachment->preview()->getLargeIconUrl() . '">';
	     		$return.= '<span class="filename">' . str_excerpt(clean($attachment->getName()), 15) .'</span>';
	     		$return.= '</a></li>';
	     	} // foreach
	     	$return.= '</ul></div>';
	     	return $return;
	    } else {
	      return '<!-- No Attachments -->';
	    } // if
     	
    } // if
  } // object_attachments