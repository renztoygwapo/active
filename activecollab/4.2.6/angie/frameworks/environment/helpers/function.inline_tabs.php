<?php

  /**
   * Inline tabs helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Prints the inline tabs structure for specified $object
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_inline_tabs($params, &$smarty) {
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface());
    
    $object = array_var($params, 'object');
    if ($object) {    
	    $inline_tabs = new NamedList();
	    $logged_user = Authentication::getLoggedUser();
	    EventsManager::trigger('on_inline_tabs', array(&$inline_tabs, &$object, &$logged_user, $interface));
    } else {
    	$inline_tabs = array_var($params, 'inline_tabs');
    } // if
    
    if (count($inline_tabs)) {
      $smarty->assign(array(
        '_smarty_function_inline_tabs' => $inline_tabs,
        '_smarty_function_inline_tabs_id' => array_var($params, 'id', 'inline_tabs_'.time())
      ));

      return $smarty->fetch(get_view_path('_inline_tabs', null, ENVIRONMENT_FRAMEWORK));
    } else {
      return '';
    } // if
  } // smarty_function_inline_tabs