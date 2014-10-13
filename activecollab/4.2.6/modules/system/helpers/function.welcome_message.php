<?php
  /**
   * Class description
   *
   * @package
   * @subpackage
   */

  /**
   * Render welcome message box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_welcome_message($params, &$smarty) {
    AngieApplication::useWidget('welcome_message', SYSTEM_MODULE);

    $logo_classes = array('logo');

    if(ConfigOptions::getValue('identity_logo_on_white')) {
      $logo_classes[] = 'logo_on_white';
    } // if

    $result = '<div class="welcome_message">
      <div class="logo_wrapper">
        <div class="' . implode(' ', $logo_classes) . '">
          <img src="' . clean(AngieApplication::getBrandImageUrl('logo.128x128.png')) . '">
        </div>
      </div>';

    $message = ConfigOptions::getValue('identity_client_welcome_message');

    if($message) {
      if(array_var($params, 'show_title', false)) {
        $result .= '<h3 class="head"><span class="head_inner">' . lang('Welcome') . '</span></h3>';
      } // if

      $result .= '<div class="body"><div class="body_inner">' . nl2br(make_links_clickable(clean($message))) . '</div></div>';
    } // if

    return $result . '</div>';
  } // smarty_function_welcome_message