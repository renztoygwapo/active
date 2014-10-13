<?php

  /**
   * favorite_object helper implementation
   * 
   * @package angie.frameworks.favorites
   * @subpackage helpers
   */

  /**
   * Render favorite_object indicator and toggler
   *
   * @param $params
   * @param $smarty
   * @return string
   */
  function smarty_function_favorite_object($params, &$smarty) {
    $object = array_required_var($params, 'object', true, 'ICanBeFavorite');
    $user = array_required_var($params, 'user', true, 'User');
    
    $id = isset($params['id']) && $params['id'] ? $params['id'] : HTML::uniqueId('favorite_object');

    $interface = AngieApplication::getPreferedInterface();
    if (isset($params['interface'])) {
      $interface = $params['interface'];
      unset($params['interface']);
    } // if
    
    $is_favorite = Favorites::isFavorite($object, $user);
    
    $favorite_icon_url = AngieApplication::getImageUrl('heart-on.png', FAVORITES_FRAMEWORK);
    $not_favorite_icon_url = AngieApplication::getImageUrl('heart-off.png', FAVORITES_FRAMEWORK);
    
    if ($interface == AngieApplication::INTERFACE_PRINTER) {
      if ($is_favorite) {
        return '<img src="' . $favorite_icon_url . '">';
      } else {
        return '<img src="' . $not_favorite_icon_url . '">';
      } // if
    } else {
      return '<a href="#" id="' . $id . '"></a><script type="text/javascript">$("#' . $id . '").asyncToggler(' . JSON::encode(array(
        'is_on' => $is_favorite, 
        'content_when_on' => HTML::openTag('img', array('src' => $favorite_icon_url)), 
        'content_when_off' => HTML::openTag('img', array('src' => $not_favorite_icon_url)), 
        'title_when_on' => lang('Remove from Favorites'), 
        'title_when_off' => lang('Add to Favorites'), 
        'url_when_on' => $user->favorites()->getRemoveFromFavoritesUrl($object), 
        'url_when_off' => $user->favorites()->getAddToFavoritesUrl($object),
        'success_event' => $object->getUpdatedEventName(), 
      )) . ');
      
      App.Wireframe.Events.bind("' . $object->getUpdatedEventName() . '", function (event, object) {
        if(object["class"] == "' . get_class($object) . '" && object["id"] == ' . $object->getId() . ') {
        	$("#'.$id.'").attr({
        		href : (object.is_favorite) ? "'.$user->favorites()->getRemoveFromFavoritesUrl($object).'" : "'.$user->favorites()->getAddToFavoritesUrl($object).'",
        		is_on : (object.is_favorite) ? "1" : "0",
        		title : (object.is_favorite) ? "'.lang('Remove from Favorites').'" : "'.lang('Add to Favorites').'"
        	});
        	$("#'.$id.' img").attr("src", (object.is_favorite) ? "'.$favorite_icon_url.'" : "'.$not_favorite_icon_url.'");
      	} // if
    	});
      </script>';
    } // if
  } // smarty_function_favorite_object