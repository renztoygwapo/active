<?php

  /**
   * Render specific empty slate
   * 
   * Parameters:
   * 
   * - module - module name
   * - name - template name, default is index
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_empty_slate($params, &$smarty) {
  	$module = array_var($params, 'module', SYSTEM_MODULE, true);
  	$name = array_var($params, 'name', 'index', true);

    $template = $smarty->createTemplate(AngieApplication::getViewPath($name, 'empty_slates', $module));

    if(count($params)) {
      $template->assign($params);
    } // if
  	
  	return $template->fetch();
  } // smarty_function_empty_slate