<?php

  /**
  * Assign template vars to javascript
  * 
  * This function will make available all template vars to JavaScript by 
  * converting them to JSON
  *
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_template_vars_to_js($params, &$smarty) {
    $wireframe = array_required_var($params, 'wireframe', false, 'Wireframe');
    $wrap = array_var($params, 'wrap', true, true);
    
    if($wireframe instanceof Wireframe) {
      if(is_array($wireframe->getJavascriptVariables()) && count($wireframe->getJavascriptVariables())) {
        if (!$wrap) {
          return JSON::encode($wireframe->getJavascriptVariables());
        } else {
          return '<script type="text/javascript">App.Config.reset(' . JSON::encode($wireframe->getJavascriptVariables()) . ');</script>';
        } // if
      } else {
        return '';
      } // if
    } else {
      throw new InvalidParamError('wireframe', $wireframe, '$wireframe is expected to be an instance of Wireframe class');
    } // if
  } // smarty_function_template_vars_to_js