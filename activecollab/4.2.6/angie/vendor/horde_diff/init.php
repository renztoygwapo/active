<?php

  /**
   * Diff library
   *
   * @package angie.library.diff
   */

  const DIFF_LIB_PATH = __DIR__;

  /**
   * Render diffence between strings. Default method is 'inline', other methods are 'context' and 'unified' .
   *
   * @param string $string_1
   * @param string $string_2
   * @param string $render_method
   *
   * @return string
   */
  function render_diff($string_1, $string_2, $render_method = 'inline') {
    require_once DIFF_LIB_PATH . '/Diff.php';
    require_once DIFF_LIB_PATH . '/Diff/Renderer.php';
    require_once DIFF_LIB_PATH . '/Diff/Renderer/Inline.php';
    require_once DIFF_LIB_PATH . '/Diff/Renderer/Context.php';
    require_once DIFF_LIB_PATH . '/Diff/Renderer/Unified.php';

    require_once DIFF_LIB_PATH . '/Diff/Engine/Native.php';
    require_once DIFF_LIB_PATH . '/Diff/Engine/Shell.php';
    require_once DIFF_LIB_PATH . '/Diff/Engine/String.php';
    require_once DIFF_LIB_PATH . '/Diff/Engine/Xdiff.php';

    require_once DIFF_LIB_PATH . '/Diff/Op/Base.php';
    require_once DIFF_LIB_PATH . '/Diff/Op/Add.php';
    require_once DIFF_LIB_PATH . '/Diff/Op/Change.php';
    require_once DIFF_LIB_PATH . '/Diff/Op/Copy.php';
    require_once DIFF_LIB_PATH . '/Diff/Op/Delete.php';

    require_once DIFF_LIB_PATH . '/Util/String.php';

    $lines_1 = strpos($string_1, "\n") ? explode("\n", $string_1) : array($string_1);
    $lines_2 = strpos($string_2, "\n") ? explode("\n", $string_2) : array($string_2);

    $diff = new Horde_Text_Diff('auto', array($lines_1, $lines_2));
    if ($render_method === 'inline') {
      $renderer = new Horde_Text_Diff_Renderer_Inline();
    } else if ($render_method === 'context') {
      $renderer = new Horde_Text_Diff_Renderer_Context();
    } else if ($render_method === 'unified') {
      $renderer = new Horde_Text_Diff_Renderer_Unified();
    } else {
      throw new InvalidParamError('renderer_method', $render_method, '$renderer_method can be inline, context or unified');
    } //if

    return $renderer->render($diff);
  } // render_diff