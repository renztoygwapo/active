<?php

  /**
   * Select version helper
   *
   * @package activeCollab.modules.text_compare
   * @subpackage helpers
   */

  /**
   * Render select version select box
   * 
   * Params:
   * 
   * - versions - All available versions
   * - version - Version value
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_version($params, &$smarty) {
    $version_label = array_var($params, 'version_label', null, true);
    
    $versions = array_var($params, 'versions', null, true);
    if(is_foreachable($versions)) {
      foreach($versions as $version) {
        $options[] = option_tag(lang('Version #:version', array('version' => $version['version'])), $version['version'], array(
          'selected' => $version['version'] == $version_label,
        ));
      } // foreach
    } // if
    
    return select_box($options, $params);
  } // smarty_function_select_version

?>