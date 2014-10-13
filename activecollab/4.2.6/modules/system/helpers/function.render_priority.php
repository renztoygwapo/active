<?php

  /**
   * Renders priority pill
   * 
   * Params:
   * 
   * - priority_id - Number representing priority
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_render_priority($params, &$smarty) {
    $priority_id = array_var($params, 'priority_id', PRIORITY_NORMAL);
    $mode = array_var($params, 'mode', 'pill');

    if (!in_array($priority_id, array(-2, -1, 1, 2))) {
      return '';
    } // if

    switch($priority_id) {
      case -2:
        $priority_text = lang('Lowest'); $priority_class = 'not_important'; $priority_image = 'priority-lowest.png'; break;
      case -1:
        $priority_text = lang('Low'); $priority_class = 'not_important';  $priority_image = 'priority-low.png'; break;
      case 1:
        $priority_text = lang('High'); $priority_class = 'important';  $priority_image = 'priority-high.png';break;
      case 2:
        $priority_text = lang('Highest'); $priority_class = 'important';  $priority_image = 'priority-highest.png'; break;
      default:
        $priority_text = lang('Normal'); $priority_class = 'normal'; $priority_image = 'priority-normal.png';
    } // switch

    if ($mode == 'pill') {
      return '<span class="pill priority ' . $priority_class . '">' . $priority_text . '</span>';
    } else {
      return '<span class="priority"><img src="' . AngieApplication::getImageUrl("priority-widget/{$priority_image}", COMPLETE_FRAMEWORK) . '" alt="' . $priority_text . '" title="' . $priority_text . '"></span>';
    } // if
  } // smarty_function_render_priority