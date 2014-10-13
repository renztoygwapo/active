<?php

/**
 * notification_invoice_items_table helper implementation
 *
 * @package activeCollab.modules.invoicing
 * @subpackage helpers
 */

/**
 * Render notification invoice item table
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_notification_invoice_items_table($params, &$smarty) {
  $context = array_required_var($params, 'context', true, 'ApplicationObject');
  $recipient = array_required_var($params, 'recipient', true, 'IUser');

  $invoice_template = new InvoiceTemplate();
  $invoice_currency = $context->getCurrency();
  $invoice_language = $context->getLanguage();

  $language = $recipient->getLanguage();

  $separator_row = '<tr><td align="left" colspan="6">&nbsp;</td></tr>';

  $content = '<table cellpadding="0" cellspacing="0" style="text-align: left" bgcolor="#ffffff" width="100%">';
  $content .= '<tr style="font-weight: bold; background-color: #e8e8e8;">';
  $content .= '<td style="width: 13px; padding: 2px; padding-rigth: 5px; text-align: right;">#</td>';
  $content .= '<td style="padding: 2px;">' . lang("Description", null, null, $language) . '</td>';
  $content .= '<td style="width: 56px; padding: 2px; text-align: center;">' . lang("Qty.", null, null, $language) . '</td>';
  $content .= '<td style="width: 86px; padding: 2px; text-align: right;">' . lang("Unit Cost", null, null, $language) . '</td>';

  if ($context->getSecondTaxIsEnabled()) {
    $content .= '<td style="width: 56px; padding: 2px; text-align: center;">' . lang('Tax #1', null, null, $language) . '</td>';
    $content .= '<td style="width: 56px; padding: 2px; text-align: center;">' . lang('Tax #2', null, null, $language) . '</td>';
  } else {
    $content .= '<td style="width: 56px; padding: 2px; text-align: center;">' . lang("Tax", null, null, $language) . '</td>';
  } // if

  $content .= '<td style="width: 56px; padding: 2px; text-align: right;">' . lang("Total", null, null, $language) . '</td>';
  $content .= '</tr>' . $separator_row;



  $items = $context->getItems();
  if($items)  {
    foreach($items as $item) {
      $content .= '<tr>';
      $content .= '<td style="padding: 2px; padding-right: 5px; text-align: right;">'. ($item->getPosition() + 1) . '.</td>';
      $content .= '<td style="padding: 2px;">'. $item->getFormattedDescription() . '</td>';
      $content .= '<td style="padding: 2px; text-align: center;">'. $item->getQuantity() . '</td>';
      $content .= '<td style="padding: 2px; text-align: right;">'. $invoice_currency->format($item->getUnitCost()) . '</td>';

      if ($invoice_template->getDisplayTaxRate()) {
        $content .= '<td style="padding: 2px; text-align: center;">'. ($item->getFirstTaxRatePercentageVerbose() ? $item->getFirstTaxRatePercentageVerbose() : '-') . '</td>';
        if ($context->getSecondTaxIsEnabled()) {
          $content .= '<td style="padding: 2px; text-align: center;">'. ($item->getSecondTaxRatePercentageVerbose() ? $item->getSecondTaxRatePercentageVerbose() : '-') . '</td>';
        } // if
      } else if ($invoice_template->getDisplayTaxAmount()) {
        $content .= '<td style="padding: 2px; text-align: center;">'. Globalization::formatMoney($item->getFirstTax(), $invoice_currency, $invoice_language) . '</td>';
        if ($context->getSecondTaxIsEnabled()) {
          $content .= '<td style="padding: 2px; text-align: center;">'. Globalization::formatMoney($item->getSecondTax(), $invoice_currency, $invoice_language) . '</td>';
        } // if
      } // if

      $content .= '<td style="padding: 2px; text-align: right;">'. $invoice_currency->format($item->getTotal()) . '</td>
        </tr>';
    } // foreach
  } // if

  $content .= '</table>';

  $content .= '<table cellpadding="0" cellspacing="0" border="0" style="margin-top: 36px;" bgcolor="#ffffff" width="100%">
      <tr>
        <td style="padding: 2px; text-align: right;">' . lang('Subtotal', null, null, $language) . '</td>
        <td style="padding: 2px; width: 56px; text-align: right;">' . $invoice_currency->format($context->getSubTotal()) . '</td>
      </tr>
      <tr>
        <td style="padding: 2px; text-align: right;">' . lang('Tax', null, null, $language) . '</td>
        <td style="padding: 2px; width: 56px; text-align: right;">' . $invoice_currency->format($context->getTax()) . '</td>
      </tr>
      <tr>
        <td style="padding: 2px; text-align: right;">' . lang('Rounding Difference', null, null, $language) . '</td>
        <td style="padding: 2px; width: 56px; text-align: right;">' . $invoice_currency->format($context->getRoundingDifference()) . '</td>
      </tr>
      <tr>
        <td style="padding: 2px; text-align: right; font-weight: bold; border-bottom: 1px solid #eee;">' . lang('Total Cost', null, null, $language) . '</td>
        <td style="padding: 2px; width: 56px; text-align: right; font-weight: bold; border-bottom: 1px solid #eee;">' . $invoice_currency->format($context->getTotal(true)) . '</td>
      </tr>
      <tr>
        <td style="padding: 2px; text-align: right;">' . lang('Amount Paid', null, null, $language) . '</td>
        <td style="padding: 2px; width: 56px; text-align: right;">' . $invoice_currency->format($context->getPaidAmount()) . '</td>
      </tr>
      <tr>
        <td style="padding: 2px; text-align: right; font-weight: bold; border-bottom: 1px solid #eee;">' . lang('Balance Due', null, null, $language) . '</td>
        <td style="padding: 2px; width: 56px; text-align: right; font-weight: bold; border-bottom: 1px solid #eee;">' . $context->getCurrencyCode() . ' ' . $invoice_currency->format($context->getBalanceDue()) . '</td>
      </tr>
    </table>';

  return $content;
} // smarty_function_notification_invoice_items_table