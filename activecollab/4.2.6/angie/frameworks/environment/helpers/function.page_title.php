<?php

  /**
   * Display page title
   *
   * Parameters:
   * 
   * - default - default page title, used when no page title is present
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_page_title($params, &$smarty) {
    return $smarty->getVariable('wireframe')->value->getPageTitle() ? clean($smarty->getVariable('wireframe')->value->getPageTitle()) : clean(array_var($params, 'default', 'Index'));
  } // smarty_function_page_title