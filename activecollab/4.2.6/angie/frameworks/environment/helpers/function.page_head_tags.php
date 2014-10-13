<?php

  /**
   * page_head_tags helper implementation
   */

  /**
   * Render head tags collected from Wireframe
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_page_head_tags($params, &$smarty) {
    return @implode("\n", $smarty->getVariable('wireframe')->value->getAllHeadTags());
  } // smarty_function_page_head_tags