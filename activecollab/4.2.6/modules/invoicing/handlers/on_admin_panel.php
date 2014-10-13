<?php

  /**
   * on_admin_panel event handler
   * 
   * @package angie.frameworks.invoicing
   * @subpackage handlers
   */

  /**
   * Handle on_admin_panel event
   * 
   * @param AdminPanel $admin_panel
   */
  function invoicing_handle_on_admin_panel(AdminPanel &$admin_panel) {
    // invoicing settings
    $admin_panel->addToInvoicing('invoicing_settings', lang('Invoicing Settings'), Router::assemble('invoicing_settings'), AngieApplication::getImageUrl('admin_panel/invoice-settings.png', INVOICING_MODULE),array(
    	'onclick' => new FlyoutFormCallback('invoicing_settings_updated'), 
    ));

    // invoice designer
    $admin_panel->addToInvoicing('invoice_designer', lang('Invoice Designer'), Router::assemble('admin_invoicing_pdf'), AngieApplication::getImageUrl('admin_panel/invoice-designer.png', INVOICING_MODULE));

    // item templates
    $admin_panel->addToInvoicing('item_templates', lang('Item Templates'), Router::assemble('admin_invoicing_items'), AngieApplication::getImageUrl('admin_panel/item-templates.png', INVOICING_MODULE), array(
      'onclick' => new FlyoutCallback(),
    ));

    // note templates
    $admin_panel->addToInvoicing('note_templates', lang('Note Templates'), Router::assemble('admin_invoicing_notes'), AngieApplication::getImageUrl('admin_panel/note-templates.png', INVOICING_MODULE), array(
      'onclick' => new FlyoutCallback(),
    ));

    // tax rates
    $admin_panel->addToInvoicing('tax_rates', lang('Tax Rates'), Router::assemble('admin_tax_rates'), AngieApplication::getImageUrl('admin_panel/tax-rates.png', INVOICING_MODULE), array(
      'onclick' => new FlyoutCallback(),
    ));

    // invoice overdue reminders
    $admin_panel->addToInvoicing('invoice_overdue_reminders', lang('Overdue Reminders'), Router::assemble('admin_invoicing_invoice_overdue_reminders'), AngieApplication::getImageUrl('admin_panel/invoice-overdue-reminders.png', INVOICING_MODULE), array(
      'onclick' => new FlyoutFormCallback(array(
        'success_message' => lang('Settings updated'),
        'title' => lang('Automatic Invoice Overdue Reminders'),
        'width' => 900
      )),
    ));
  } // invoicing_handle_on_admin_panel