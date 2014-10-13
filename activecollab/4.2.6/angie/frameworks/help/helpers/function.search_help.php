<?php

  /**
   * search_help widget implementation
   *
   * @package angie.frameworks.help
   * @subpackage helpers
   */

  /**
   * Render search help widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_search_help($params, &$smarty) {
    AngieApplication::useWidget('search_help', HELP_FRAMEWORK);

    $id = array_var($params, 'id');

    if(empty($id)) {
      $id = HTML::uniqueId('search_help');
    } // if

    return '<div class="search_help" id="' . $id . '">
      <form method="get" action="' . Router::assemble('help_search') . '" class="search_help_form">
        <input type="text" name="query" placeholder="' . lang('Search Help for Answers') . '">
      </form>
      <div class="search_help_results"></div>
    </div><script type="text/javascript">$("#' . $id . '").searchHelp();</script>';
  } // smarty_function_search_help