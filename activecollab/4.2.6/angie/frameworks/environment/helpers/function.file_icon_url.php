<?php

  /**
   * Return file icon URL
   * 
   * Parameters:
   * 
   * - filename - filename
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_file_icon_url($params, &$smarty) {
		$filename = array_var($params, 'filename', 'null');
  	return AngieApplication::getFileIconUrl($filename, '16x16');
  } // smarty_function_image_url