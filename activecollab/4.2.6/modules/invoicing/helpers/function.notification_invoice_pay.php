<?php

  /**
   * notification_invoice_pay helper implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage helpers
   */

  /**
   * Return payment link for given invoice, if needed
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_notification_invoice_pay($params, &$smarty) {
    $context = array_required_var($params, 'context', true, 'ApplicationObject');
    $context_view_url = array_required_var($params, 'context_view_url');
    $recipient = array_required_var($params, 'recipient', true, 'IUser');

    if($context->isIssued()) {

      if($recipient->isFinancialManager()) {
        $label = lang('Go to Invoice', null, true, $recipient->getLanguage());
        $url = $context_view_url;
      } else {
        if($context->payments()->canMakePublicPayment()) {
          $label = lang('Pay Online Now', null, true, $recipient->getLanguage());
          $url = $context->payments()->getPublicUrl();
        } //if
      }//if
      if($label && $url) {
        return '<table style="margin-top: 16px;" bgcolor="#ffffff" width="100%"><tr><td style="font-size: 120%; text-align: center;"><a href="' . clean($url) . '" style="' . AngieApplication::mailer()->getDecorator()->getLinkStyle() . '" target="_blank">' . $label . '</a></td></tr></table>';
      }//if

    } // if
  } // smarty_function_notification_invoice_pay