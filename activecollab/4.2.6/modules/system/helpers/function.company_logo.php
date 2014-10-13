<?php

  /**
   * company_logo helper implementation
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render company logo image tag
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_company_logo($params, &$smarty) {
    $company = array_var($params, 'company', null, true);
    $size = array_var($params, 'size', null, true) == 'large' ? '40x40' : '16x16';
    $url_only =array_var($params, 'url', false);
    
    $company_id = $company instanceof Company ? $company->getId() : (integer) $company;
    $logo_url = get_company_logo_url($company_id, $size);

    if ($url_only) {
    	return $logo_url;
    } else {
    	$params['src'] = $logo_url;
    	return open_html_tag('img', $params, true);
    } // if
  } // smarty_function_company_logo

?>