<?php

  /**
   * notification_identity helper implementation
   *
   * @package angie.frameworks.email
   * @subpackage helpers
   */

  /**
   * Render notification identity
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_notification_identity($params, &$smarty) {
    $recipient = array_required_var($params, 'recipient', false, 'IUser');

    $logo_width = (integer) array_var($params, 'logo_width', 80);
    if($logo_width < 1) {
      $logo_width = 80;
    } // if

    $result = '<img src="' . clean(AngieApplication::getBrandImageUrl("logo.{$logo_width}x{$logo_width}.png", true)) . '" alt="Logo">';

    if(array_var($params, 'show_site_name', true)) {
      $identity_name = ConfigOptions::getValue('identity_name');

      if(empty($identity_name)) {
        $identity_name = lang(':company Projects', array(
          'company' => Companies::findOwnerCompany()->getName(),
        ), true, $recipient->getLanguage());
      } // if

      $result .= '<br><span style="font-size:14px; padding: 0 0 10px 0;">' . clean($identity_name) . '</span>';
    } // if

    return $result;
  } // smarty_function_notification_identity