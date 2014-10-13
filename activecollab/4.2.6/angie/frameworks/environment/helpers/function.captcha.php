<?php

  /**
  * Render captcha field
  * 
  * Parameters:
  * 
  * - name - field name
  * - value - initial value
  * - array of additional attributes
  * - captcha_url - URL of captcha script
  *
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_captcha($params, &$smarty) {
    return "<span class='captcha'>" . HTML::input($params['name'], '', $params) . "<img src='" . AngieApplication::getProxyUrl('captcha', ENVIRONMENT_FRAMEWORK_INJECT_INTO, array( 'id' => md5(time()), )) . "' class='captcha' alt='verification' />" .  '</span>';
  } // smarty_function_captcha