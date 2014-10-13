<?php

  /**
   * notification_invoice_info helper implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage helpers
   */

  /**
   * Render notification invoice info table
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_notification_invoice_info($params, &$smarty) {
    $context = array_required_var($params, 'context', true, 'ApplicationObject');
    $recipient = array_required_var($params, 'recipient', true, 'IUser');

    $language = $recipient->getLanguage();
    $currency = $context->getCurrency();

    if($context->isDraft()) {
      $label = lang('Draft #', null, true, $language);
      $number = $context->getId();
    } else {
      $label = lang('Invoice #', null, true, $language);
      $number = clean($context->getNumber());
    } //if

    $result = '<table cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" width="100%" style="margin-bottom: 36px;">
      <tr style="font-weight: bold; font-size: 18px;"><td style="width: 120px; padding: 5px;">' . $label . '</td><td style="padding: 5px;">' . $number . '</td></tr>';

    $client = $context->getCompany();

    if($client instanceof Company) {
      $client_label = lang('Client', null, true, $language);
      $client_name = $client->getName();

      $project = $context->getProject();
      if($project instanceof Project && $project->getState() > STATE_TRASHED) {
        $client_label .= ' (' . lang('Project', null, true, $language) . ')';
        $client_name .= ' (' . $project->getName() . ')';
      } // if

      $result .= '<tr><td style="width: 120px; padding: 5px;">' . $client_label . ':</td><td style="padding: 5px;">' . clean($client_name) . '</td></tr>';
    } // if

    if($context->getPurchaseOrderNumber()) {
      $result .= '<tr><td style="width: 120px; padding: 5px;">' . lang('Purchase Order #', null, true, $language) . ':</td><td style="padding: 5px;">' . clean($context->getPurchaseOrderNumber()) . '</td></tr>';
    } // if

    if(!$context->isDraft()) {
      $result .= '<tr><td style="width: 120px; padding: 5px;">' . lang('Issued On', null, true, $language) . ':</td><td style="padding: 5px;">' . clean($context->getIssuedOn()->formatDateForUser($recipient, 0)) . '</td></tr>';
    } // if

    $result .= '<tr><td style="width: 120px; padding: 5px;">' . lang('Balance Due', null, true, $language) . ':</td><td style="padding: 5px;">' . Globalization::formatMoney($context->getBalanceDue(), $currency, $language, true, true) . '</td></tr>';


    // Issued invoice, but not yet paid... Show due date
    if($context->isIssued()) {
      if($context->isOverdue()) {
        $due_on = '<span style="color: red; font-weight: bold;">' . clean($context->getDueOn()->formatForUser($recipient, 0)) . '</span>';
      } else {
        $due_on = clean($context->getDueOn()->formatForUser($recipient, 0));
      } // if

      $result .= '<tr><td style="width: 120px; padding: 5px;">' . lang('Payment Due On', null, true, $language) . ':</td><td style="padding: 5px;">' . $due_on . '</td></tr>';

    // Paid Invoice...
    } elseif($context->isPaid()) {
      $result .= '<tr><td style="width: 120px; padding: 5px;">' . lang('Paid On', null, true, $language) . ':</td><td style="padding: 5px;">' . clean($context->getClosedOn()->formatDateForUser($recipient, 0)) . '</td></tr>';

    // Canceled Invoice
    } elseif($context->isCanceled()) {
      $result .= '<tr><td style="width: 120px; padding: 5px;">' . lang('Canceled On', null, true, $language) . ':</td><td style="padding: 5px;">' . clean($context->getClosedOn()->formatDateForUser($recipient, 0)) . '</td></tr>';

    // Draft
    } else {
      $result .= '<tr><td style="width: 120px; padding: 5px;">' . lang('Created On', null, true, $language) . ':</td><td style="padding: 5px;">' . clean($context->getCreatedOn()->formatDateForUser($recipient, 0)) . '</td></tr>';
    } // if

    $result .= '<tr><td style="padding: 15px 5px 5px 5px;" colspan="2">' . lang('The PDF file containing invoice details is attached to this email', null, true, $language) . '.</td></tr>';


    return "$result</table>";
  } // smarty_function_notification_invoice_info