<?php

  /**
   * Render select paper format
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_paper_format($params, &$smarty) {
    $value = array_var($params, 'value', null, true);
        
    $paper_formats = array(
      Globalization::PAPER_FORMAT_A4,
      Globalization::PAPER_FORMAT_LETTER
    );
    
    $options = array();
    
    foreach ($paper_formats as $paper_format) {
    	$option_attributes = array();
    	if ($paper_format == $value) {
				$option_attributes['selected'] = true;
    	} // if
			$options[] = option_tag($paper_format, $paper_format, $option_attributes);
    } // foreach
    
    return select_box($options, $params);
  } // select_papaer_format