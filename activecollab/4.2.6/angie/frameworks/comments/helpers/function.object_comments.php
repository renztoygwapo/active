<?php

  /**
   * Implementation of object_comments helper
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * List object comments
   * 
   * Parameters:
   * 
   * - object - Parent object. It needs to be an instance of ProjectObject class
   * - comments - List of comments. It is optional. If it is missing comments 
   *   will be loaded by calling getCommetns() method of parent object
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_comments($params, &$smarty) {
    AngieApplication::useWidget('select_attachments', FILE_UPLOADER_FRAMEWORK);

    $object = array_required_var($params, 'object', true, 'IComments');
    $user = array_required_var($params, 'user', true, 'IUser');
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('object_comments');
    } // if
    
    if(isset($params['class']) && $params['class']) {
      $params['class'] .= ' object_comments';
    } else {
      $params['class'] = 'object_comments';
    } // if
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    // Default, web interface
    if($interface == AngieApplication::INTERFACE_DEFAULT) {
      AngieApplication::useWidget('object_comments', COMMENTS_FRAMEWORK);

      $count = (integer) array_var($params, 'count', 5, true); // Number of recent comment that need to be loaded initially
      
      $min_last_visit = new DateTimeValue(time() - 2592000); // 30 days...
      $last_visit = $user->getLastVisitOn(true);
    	
    	if($last_visit && $last_visit->getTimestamp() < $min_last_visit->getTimestamp()) {
	      $last_visit = $min_last_visit;
	    } // if
      
      $total_comments = $object->comments()->count($user);
      
      $new_comments_count = $total_comments ? $object->comments()->countSinceVisit($user, $last_visit) : 0;
      if($new_comments_count && $count < $new_comments_count) {
        $count = $new_comments_count + 1;
      } // if
      
      $options = array(
        'total_comments' => $total_comments,  
      	'comments_url' => $object->comments()->getUrl(), 
      	'user_id' => $user->getId(), 
      	'user_email' => $user->getEmail(), 
      );

      $subscribers = $object->subscriptions()->get();
      
      $options['object'] = array(
        'id' => $object->getId(),
        'class'	=> get_class($object),
        'verbose_type' => $object->getVerboseType(false, $user->getLanguage()),
        'verbose_type_lowercase'	=> $object->getVerboseType(true, $user->getLanguage()),
        'permissions' => array('can_comment' => $object->comments()->canComment($user)),
        'event_names' => array('updated' => $object->getUpdatedEventName(), 'deleted' => $object->getDeletedEventName()),
        'is_locked' => $object->getIsLocked(),
        'is_completed' => $object instanceof IComplete && $object->getCompletedOn() instanceof DateValue,
        'subscribers' => $subscribers ? JSON::valueToMap($subscribers) : null,
        'urls' => array(
          'subscriptions' => $object->subscriptions()->getSubscriptionsUrl()
        )
      );

      $current_request = $smarty->getVariable('request')->value;
      $options['event_scope'] = $current_request->getEventScope();

      if ($object instanceof ILabel) {
      	$options['object']['label']['id'] = $object->getLabelId();
      } // if
      
      if ($object instanceof ICategory) {
      	$options['object']['category']['id'] = $object->getCategoryId();
      } // if
      
      $options['comments'] = Comments::findForWidget($object, $user, 0 , 500);
      
      if(empty($params['load_timestamp'])) {
        $params['load_timestamp'] = time();
      } // if
      
      $result = HTML::openTag('div', $params);
      
			$view = $smarty->createTemplate(get_view_path('_object_comment_form_row', null, COMMENTS_FRAMEWORK));
      $view->assign(array(
      	'comment_parent' => $object, 
        'comment' => $object->comments()->newComment(),  
        'comments_id' => $params['id'],
        'comment_data' => array(
          'body' => null
        ),
        'user' => $user,
      ));      
      $result .= $view->fetch();
      
      return $result . '</div><script type="text/javascript">$("#' . $params['id'] . '").objectComments(' . JSON::encode($options) . ');</script>'; 
    
    // Phone interface
  	} elseif($interface == AngieApplication::INTERFACE_PHONE) {
      AngieApplication::useHelper('image_url', ENVIRONMENT_FRAMEWORK);
  		
    	$options = array(
    		'comments' => array()
    	);
    	
      if($object->comments()->get($user)) {
        foreach($object->comments()->get($user) as $comment) {
	        $options['comments'][] = $comment->describe($user, true, $interface);
	      } // if
      } // if
      
      $result = HTML::openTag('div', $params);
      
      if(is_foreachable($options['comments'])) {
      	$result .= '<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="p">
	      	<li data-role="list-divider"><img src="' . smarty_function_image_url(array(
						'name' => 'icons/listviews/navigate.png',
						'module' => COMMENTS_FRAMEWORK,
						'interface' => AngieApplication::INTERFACE_PHONE
					), $smarty) . '" class="divider_icon" alt="">' . lang('Comments') . '</li>';
      	
      	AngieApplication::useHelper('datetime', GLOBALIZATION_FRAMEWORK, 'modifier');
	      
	      foreach($options['comments'] as $comment) {
	      	$result .= '<li>
	      		<img class="ui-li-icon" src=' . $comment['created_by']['avatar']['large'] . ' alt=""/>
	      		<p class="comment_details ui-li-desc">By <a class="ui-link" href="' . $comment['created_by']['permalink'] . '">' . $comment['created_by']['short_display_name'] . '</a> on ' . smarty_modifier_datetime($comment['created_on']) . '</p>
	      		<div class="comment_overflow ui-li-desc">' . nl2br($comment['body']) . '</div>';
	      	
	      	if(is_foreachable($comment['attachments'])) {
	      		foreach($comment['attachments'] as $attachment) {
	      			$result .= '<div class="comment_attachment"><a href="' . $attachment['urls']['view'] . '" target="_blank"><img src="' . $attachment['preview']['icons']['large'] . '" /><span class="filename">' . $attachment['name'] . '</span></a></div>';
	      		} // forech
	      	} // if
	      	
	      	$result .= '</li>';
	      } // foreach
				
			  $result .= '</ul>';
      } // if
      
      return $result . '</div><script type="text/javascript">$("div.comment_overflow").find("p img").css("max-width", "100%"); // fit comment images to screen</script>';
    
  	// Print interface
  	} elseif ($interface == AngieApplication::INTERFACE_PRINTER) {
    	$comments = $object->comments()->get($user);

    	AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');
    	AngieApplication::useHelper('object_attachments',ATTACHMENTS_FRAMEWORK);
    	 
    	if (is_foreachable($comments)) {
    	  $result = '<div class="object_comments"><h2 class="comments_title">' . lang('Comments') . '</h2>';
    	  $result .= '<table cellspacing="0">';
    	  
    		foreach ($comments as $comment) {
    		    $result .= '<tr>';
    			$result .= 	'<td class="comment_avatar"><img src="' . $comment->getCreatedBy()->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG) . '"></td>';
   				$result .= 	'<td class="comment_body">';
   				$result .=		'<div class="comment">';
   				$result	.=			'<img class="comment_background" src="' . AngieApplication::getImageUrl('layout/comment-background.png', COMMENTS_FRAMEWORK, AngieApplication::INTERFACE_PRINTER) . '" />';
   				$result	.=			'<div class="comment_details"><span class="comment_author">' . $comment->getCreatedBy()->getName(true) . '</span><span class="comment_date date">' . smarty_modifier_date($comment->getCreatedOn()) . '</span></div>';
   				$result .=			'<div class="comment_body">' . HTML::toRichText($comment->getBody(), AngieApplication::INTERFACE_PRINTER) . smarty_function_object_attachments(array('object' => $comment, 'interface' => $interface, 'user' => $user), $smarty) . '</div>';
   				$result .=		'</div>';
   				$result .=  '</td>';
					$result .= '</tr>';
				}//foreach
			$result.= '</table></div>';
    	} // if
    	
    	
    	return $result;
    	
	  // Other interfaces
    } else {

      $options = array('comments' => null);
      
      $comments = $object->comments()->get($user);
      if($comments) {
        $options['comments'] = array();
        
        foreach($comments as $comment) {
          $options['comments'][] = $comment->describe($user, true, $interface);
        } // foreach
      } // if
      
      return HTML::openTag('div', $params) . '</div><script type="text/javascript">$("#' . $params['id'] . '").objectComments(' . JSON::encode($options, $user) . ');</script></div>';
    } // if
  } // smarty_function_object_comments