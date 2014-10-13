<?php

  /**
   * Class description
   *
   * @package activeCollab.modules.invoicing
   * @subpackage helpers
   */

  /**
   * Render select counter padding box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_counter_padding($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', null, true);

    return HTML::selectFromPossibilities($name, array(
      '' => lang('Use Plain Counter Value'),
      '2' => lang('Fix to :num Letters', array('num' => 2)),
      '3' => lang('Fix to :num Letters', array('num' => 3)),
      '4' => lang('Fix to :num Letters', array('num' => 4)),
      '5' => lang('Fix to :num Letters', array('num' => 5)),
      '6' => lang('Fix to :num Letters', array('num' => 6)),
      '7' => lang('Fix to :num Letters', array('num' => 7)),
      '8' => lang('Fix to :num Letters', array('num' => 8)),
      '9' => lang('Fix to :num Letters', array('num' => 9)),
    ), $value, $params);
  } // smarty_function_select_counter_padding