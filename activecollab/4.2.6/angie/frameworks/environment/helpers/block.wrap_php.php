<?php

  /**
   * Assign generated value to template
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   */
  function smarty_block_wrap_php($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    return "<?php\n$content";
  } // smarty_block_assign