<?php

  /**
   * Invoicing module defintiion
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class InvoicingModule extends AngieModule {
    
    /**
     * Plain module name
     *
     * @var string
     */
    protected $name = 'invoicing';
    
    /**
     * Module version
     *
     * @var string
     */
    protected $version = '4.0';
    
    // ---------------------------------------------------
    //  Events and Routes
    // ---------------------------------------------------
    
    /**
     * Define module routes
     */
    function defineRoutes() {
      Router::map('invoices', 'invoices', array('controller' => 'invoices'));
      Router::map('invoices_add', 'invoices/add', array('controller' => 'invoices', 'action' => 'add'));
      
      Router::map('invoices_archive', 'invoices/archive', array('controller' => 'invoices', 'action' => 'archive'));

      Router::map('invoice', 'invoices/:invoice_id', array('controller' => 'invoices', 'action' => 'view'), array('invoice_id' => Router::MATCH_ID));

	    // Invoice Footprints
	    if (AngieApplication::isModuleLoaded('footprints')) {
		    AngieApplication::getModule('footprints')->defineAccessLogRoutesFor('invoice', 'invoices/:invoice_id', 'invoices', INVOICING_MODULE, array('invoice_id' => Router::MATCH_ID));
		    AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('invoice', 'invoices/:invoice_id', 'invoices', INVOICING_MODULE, array('invoice_id' => Router::MATCH_ID));
	    } // if
      
      Router::map('invoice_issue', 'invoices/:invoice_id/issue', array('controller' => 'invoices', 'action' => 'issue'), array('invoice_id' => Router::MATCH_ID));
      Router::map('invoice_edit', 'invoices/:invoice_id/edit', array('controller' => 'invoices', 'action' => 'edit'), array('invoice_id' => Router::MATCH_ID));
      Router::map('invoice_delete', 'invoices/:invoice_id/delete', array('controller' => 'invoices', 'action' => 'delete'), array('invoice_id' => Router::MATCH_ID));
      Router::map('invoice_cancel', 'invoices/:invoice_id/cancel', array('controller' => 'invoices', 'action' => 'cancel'), array('invoice_id' => Router::MATCH_ID));
      Router::map('invoice_pdf', 'invoices/:invoice_id/pdf', array('controller' => 'invoices', 'action' => 'pdf'), array('invoice_id' => Router::MATCH_ID));
      Router::map('invoice_time', 'invoices/:invoice_id/time', array('controller' => 'invoices', 'action' => 'time'), array('invoice_id' => Router::MATCH_ID));
      Router::map('invoice_items_release', 'invoices/:invoice_id/items/release', array('controller' => 'invoices', 'action' => 'items_release'), array('invoice_id' => Router::MATCH_ID));
      Router::map('invoice_notify', 'invoices/:invoice_id/notify', array('controller' => 'invoices', 'action' => 'notify'), array('invoice_id' => Router::MATCH_ID));
      Router::map('invoice_change_language', 'invoices/:invoice_id/change-language', array('controller' => 'invoices', 'action' => 'change_language'), array('invoice_id' => Router::MATCH_ID));

      Router::map('invoice_public_payment_info', 'invoices/:invoice_id/public/payment', array('controller' => 'invoices', 'action' => 'public_payment_info'), array('invoice_id' => ROUTER::MATCH_ID));


      Router::map('invoices_all_payments', 'invoices/all-payments', array('controller' => 'invoices', 'action' => 'list_payments'));
      Router::map('invoice_paid', 'invoices/:invoice_id/paid', array('controller' => 'invoices', 'action' => 'mark_as_paid'), array('invoice_id' => Router::MATCH_ID));

      AngieApplication::getModule('payments')->definePaymentRoutesFor('invoice', 'company/:company_id/invoices/:invoice_id', 'company_invoices', INVOICING_MODULE, array('invoice_id' => Router::MATCH_ID, 'company_id' => Router::MATCH_ID));
      Router::map('invoice_custom_payments_add_old', 'invoices/:invoice_id/payment/add', array('controller' => 'invoice_payments', 'action' => 'add'), array('invoice_id' => Router::MATCH_ID));
      Router::map('invoice_custom_payment_edit', 'invoices/:invoice_id/payment/:invoice_payment_id/edit', array('controller' => 'invoice_payments', 'action' => 'edit'), array('invoice_id' => Router::MATCH_ID, 'invoice_payment_id' => Router::MATCH_ID));
      Router::map('invoice_custom_payment_delete', 'invoices/:invoice_id/payment/:invoice_payment_id/delete', array('controller' => 'invoice_payments', 'action' => 'delete'), array('invoice_id' => Router::MATCH_ID, 'invoice_payment_id' => Router::MATCH_ID));

      Router::map('admin_tax_rates', 'admin/tax-rates', array('controller' => 'tax_rates_admin'));
      Router::map('admin_tax_rates_add', 'admin/tax_rates/add', array('controller' => 'tax_rates_admin', 'action' => 'add'));

      Router::map('admin_tax_rate', 'admin/tax_rates/:tax_rate_id', array('controller' => 'tax_rates_admin', 'action' => 'view'), array('tax_rate_id' => Router::MATCH_ID));
      Router::map('admin_tax_rate_edit', 'admin/tax_rates/:tax_rate_id/edit', array('controller' => 'tax_rates_admin', 'action' => 'edit'), array('tax_rate_id' => Router::MATCH_ID));
      Router::map('admin_tax_rate_delete', 'admin/tax_rates/:tax_rate_id/delete', array('controller' => 'tax_rates_admin', 'action' => 'delete'), array('tax_rate_id' => Router::MATCH_ID));
      Router::map('admin_tax_rate_set_as_default', 'admin/tax_rates/:tax_rate_id/set-as-default', array('controller' => 'tax_rates_admin', 'action' => 'set_as_default'), array('tax_rate_id' => Router::MATCH_ID));
      Router::map('admin_tax_rate_remove_default', 'admin/tax_rates/:tax_rate_id/remove-default', array('controller' => 'tax_rates_admin', 'action' => 'remove_default'), array('tax_rate_id' => Router::MATCH_ID));

      Router::map('admin_invoicing_pdf', 'admin/invoicing/pdf', array('controller' => 'pdf_settings_admin'));
      Router::map('admin_invoicing_pdf_paper', 'admin/invoicing/pdf/paper', array('controller' => 'pdf_settings_admin', 'action' => 'paper'));
      Router::map('admin_invoicing_pdf_paper_remove_background', 'admin/invoicing/pdf/paper/remove-background', array('controller' => 'pdf_settings_admin', 'action' => 'remove_background'));
      Router::map('admin_invoicing_pdf_header', 'admin/invoicing/pdf/header', array('controller' => 'pdf_settings_admin', 'action' => 'header'));
      Router::map('admin_invoicing_pdf_body', 'admin/invoicing/pdf/body', array('controller' => 'pdf_settings_admin', 'action' => 'body'));
      Router::map('admin_invoicing_pdf_footer', 'admin/invoicing/pdf/footer', array('controller' => 'pdf_settings_admin', 'action' => 'footer'));
      Router::map('admin_invoicing_pdf_sample', 'admin/invoicing/pdf/sample', array('controller' => 'pdf_settings_admin', 'action' => 'sample'));

      Router::map('admin_invoicing_pdf_settings', 'admin/invoicing/pdf-settings', array('controller' => 'pdf_settings_admin', 'action' => 'index'));
      Router::map('admin_invoicing_invoice_overdue_reminders', 'admin/invoicing/invoice-overdue-reminders', array('controller' => 'invoice_overdue_reminders_admin', 'action' => 'index'));

      Router::map('admin_invoicing_notes', 'admin/invoicing/notes', array('controller' => 'invoice_note_templates_admin', 'action' => 'index'));
      Router::map('admin_invoicing_notes_add', 'admin/invoicing/notes/add', array('controller' => 'invoice_note_templates_admin', 'action' => 'add'));

      Router::map('admin_invoicing_note', 'admin/invoicing/notes/:note_id', array('controller' => 'invoice_note_templates_admin', 'action' => 'view'), array('note_id' => Router::MATCH_ID));
      Router::map('admin_invoicing_note_edit', 'admin/invoicing/notes/:note_id/edit', array('controller' => 'invoice_note_templates_admin', 'action' => 'edit'), array('note_id' => Router::MATCH_ID));
      Router::map('admin_invoicing_note_delete', 'admin/invoicing/notes/:note_id/delete', array('controller' => 'invoice_note_templates_admin', 'action' => 'delete'), array('note_id' => Router::MATCH_ID));
      Router::map('admin_invoicing_note_set_as_default', 'admin/invoicing/notes/:note_id/set-as-default', array('controller' => 'invoice_note_templates_admin', 'action' => 'set_as_default'), array('note_id' => Router::MATCH_ID));
      Router::map('admin_invoicing_note_remove_default', 'admin/invoicing/notes/:note_id/remove-default', array('controller' => 'invoice_note_templates_admin', 'action' => 'remove_default'), array('note_id' => Router::MATCH_ID));

      Router::map('admin_invoicing_items', 'admin/invoicing/items', array('controller' => 'invoice_item_templates_admin', 'action' => 'index'));
      Router::map('admin_invoicing_items_reorder', 'admin/invoicing/items/reorder', array('controller' => 'invoice_item_templates_admin', 'action' => 'reorder'));

      Router::map('admin_invoicing_item_add', 'admin/invoicing/items/add', array('controller' => 'invoice_item_templates_admin', 'action' => 'add'));
      Router::map('admin_invoicing_item_edit', 'admin/invoicing/items/:item_id/edit', array('controller' => 'invoice_item_templates_admin', 'action' => 'edit'), array('item_id' => Router::MATCH_ID));
      Router::map('admin_invoicing_item_delete', 'admin/invoicing/items/:item_id/delete', array('controller' => 'invoice_item_templates_admin', 'action' => 'delete'), array('item_id' => Router::MATCH_ID));

      AngieApplication::getModule('reports')->defineDataFilterRoutes('detailed_invoices_filter', 'invoices/filter/detailed', 'detailed_invoices_filters', INVOICING_MODULE);
      AngieApplication::getModule('reports')->defineDataFilterRoutes('summarized_invoices_filter', 'invoices/filter/summarized', 'summarized_invoices_filters', INVOICING_MODULE);
      AngieApplication::getModule('environment')->defineStateRoutesFor('invoice', 'invoices/:invoice_id', 'invoices', INVOICING_MODULE, array('invoice_id' => Router::MATCH_ID));

      Router::map('public_invoice', 'pay-invoice/:client_id/:invoice_id/:invoice_hash', array('controller' => 'public_invoices', 'action' => 'pay'), array('client_id' => ROUTER::MATCH_ID, 'invoice_id' => ROUTER::MATCH_ID, 'invoice_hash' => ROUTER::MATCH_WORD));
      Router::map('public_invoice_pdf', 'pay-invoice/:client_id/:invoice_id/:invoice_hash/pdf', array('controller' => 'public_invoices', 'action' => 'pdf'), array('client_id' => ROUTER::MATCH_ID, 'invoice_id' => ROUTER::MATCH_ID, 'invoice_hash' => ROUTER::MATCH_WORD));

      // ---------------------------------------------------
      //  Company
      // ---------------------------------------------------

      Router::map('people_company_invoices', 'people/:company_id/invoices', array('controller' => 'company_invoices', 'action' => 'index'), array('company_id' => Router::MATCH_ID));
      Router::map('people_company_invoices_payments', 'people/:company_id/invoices/payments', array('controller' => 'company_invoices', 'action' => 'payments'), array('company_id' => Router::MATCH_ID));
      Router::map('people_company_invoice', 'people/:company_id/invoices/:invoice_id', array('controller' => 'company_invoices', 'action' => 'view'), array('company_id' => Router::MATCH_ID, 'invoice_id' => Router::MATCH_ID));
      Router::map('people_company_invoice_pdf', 'people/:company_id/invoices/:invoice_id/pdf', array('controller' => 'company_invoices', 'action' => 'pdf'), array('company_id' => Router::MATCH_ID, 'invoice_id' => Router::MATCH_ID));
      Router::map('people_company_quotes', 'people/:company_id/quotes', array('controller' => 'company_quotes', 'action' => 'index'), array('company_id' => Router::MATCH_ID));
      Router::map('people_company_quote', 'people/:company_id/quotes/:quote_id', array('controller' => 'company_quotes', 'action' => 'view'), array('company_id' => Router::MATCH_ID, 'quote_id' => Router::MATCH_ID));

      // ---------------------------------------------------
      //  Quotes
      // ---------------------------------------------------

      Router::map('quotes', 'quotes', array('controller' => 'quotes'));
      Router::map('quotes_add', 'quotes/add', array('controller' => 'quotes', 'action' => 'add'));
      Router::map('quotes_archive', 'quotes/archive', array('controller' => 'quotes', 'action' => 'archive'));

      Router::map('quote', 'quotes/:quote_id', array('controller' => 'quotes', 'action' => 'view'), array('quote_id' => Router::MATCH_ID));
      Router::map('quote_pdf', 'quotes/:quote_id/pdf', array('controller' => 'quotes', 'action' => 'pdf'), array('quote_id' => Router::MATCH_ID));
      Router::map('quote_send', 'quotes/:quote_id/send', array('controller' => 'quotes', 'action' => 'send'), array('quote_id' => Router::MATCH_ID));
      Router::map('quote_won', 'quotes/:quote_id/won', array('controller' => 'quotes', 'action' => 'won'), array('quote_id' => Router::MATCH_ID));
      Router::map('quote_lost', 'quotes/:quote_id/lost', array('controller' => 'quotes', 'action' => 'lost'), array('quote_id' => Router::MATCH_ID));
      Router::map('quote_edit', 'quotes/:quote_id/edit', array('controller' => 'quotes', 'action' => 'edit'), array('quote_id' => Router::MATCH_ID));
      Router::map('quote_notify', 'quotes/:quote_id/notify', array('controller' => 'quotes', 'action' => 'notify'), array('quote_id' => Router::MATCH_ID));
      Router::map('quote_delete', 'quotes/:quote_id/delete', array('controller' => 'quotes', 'action' => 'delete'), array('quote_id' => Router::MATCH_ID));
      Router::map('quote_save_client', 'quotes/:quote_id/save-client', array('controller' => 'quotes', 'action' => 'save_client'), array('quote_id' => Router::MATCH_ID));
      Router::map('quote_change_language', 'quotes/:quote_id/change-language', array('controller' => 'quotes', 'action' => 'change_language'), array('quote_id' => Router::MATCH_ID));

      Router::map('quote_check', 'view-quote/:quote_public_id/check', array('controller' => 'public_quotes', 'action' => 'view'), array('quote_public_id' => ROUTER::MATCH_WORD));
      Router::map('quote_public_pdf', 'view-quote/:quote_public_id/pdf', array('controller' => 'public_quotes', 'action' => 'pdf'), array('quote_public_id' => ROUTER::MATCH_WORD));

      AngieApplication::getModule('comments')->defineCommentsRoutesFor('quote', 'quotes/:quote_id', 'quotes', INVOICING_MODULE, array('quote_id' => Router::MATCH_ID));
      AngieApplication::getModule('subscriptions')->defineSubscriptionRoutesFor('quote', 'quotes/:quote_id', 'quotes', INVOICING_MODULE, array('quote_id' => Router::MATCH_ID));
      AngieApplication::getModule('attachments')->defineAttachmentsRoutesFor('quote', 'quotes/:quote_id', 'quotes', INVOICING_MODULE, array('quote_id' => Router::MATCH_ID));
      AngieApplication::getModule('environment')->defineStateRoutesFor('quote', 'quotes/:quote_id', 'quotes', INVOICING_MODULE, array('quote_id' => Router::MATCH_ID));

	    // Invoice Footprints
	    if (AngieApplication::isModuleLoaded('footprints')) {
		    AngieApplication::getModule('footprints')->defineAccessLogRoutesFor('quote', 'quotes/:quote_id', 'quotes', INVOICING_MODULE, array('quote_id' => Router::MATCH_ID));
		    AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('quote', 'quotes/:quote_id', 'quotes', INVOICING_MODULE, array('quote_id' => Router::MATCH_ID));
	    } // if

      // Invoicing
      AngieApplication::getModule('invoicing')->defineInvoiceRoutesFor('quote', 'quotes/:quote_id', 'quotes', INVOICING_MODULE, array('quote_id' => Router::MATCH_SLUG));

      Router::map('company_quote', 'people/:company_id/quotes/:quote_id', array('controller' => 'company_quotes', 'action' => 'view'), array('company_id' => Router::MATCH_ID, 'quote_id' => Router::MATCH_ID));
      Router::map('company_quote_pdf', 'people/:company_id/quotes/:quote_id/pdf', array('controller' => 'company_quotes', 'action' => 'pdf'), array('company_id' => Router::MATCH_ID, 'quote_id' => Router::MATCH_ID));

      Router::map('project_created_import_quote_comments', 'projects/:project_slug/created/import-quote-comments', array('controller' => 'project_based_on_quote_created', 'action' => 'import_quote_comments'));
      Router::map('project_created_create_milestones', 'projects/:project_slug/created/create-milestones', array('controller' => 'project_based_on_quote_created', 'action' => 'create_milestones'));

      // ---------------------------------------------------
      //  Recurring Profile
      // ---------------------------------------------------

      Router::map('recurring_profiles', 'recurring/profile', array('controller' => 'recurring_invoice'));
      Router::map('recurring_profiles_archive', 'recurring/profile/archive', array('controller' => 'recurring_invoice', 'action' => 'archive'));
      Router::map('recurring_profiles_mass_edit', 'recurring/profile/mass-edit', array('controller' => 'recurring_invoice', 'action' => 'mass_edit'));

      Router::map('recurring_profile', 'recurring/profile/:recurring_profile_id', array('controller' => 'recurring_invoice', 'action' => 'view'), array('recurring_profile_id' => Router::MATCH_ID));
      Router::map('recurring_profile_add', 'recurring/profile/add', array('controller' => 'recurring_invoice', 'action' => 'add'));
      Router::map('recurring_profile_edit', 'recurring/profile/:recurring_profile_id/edit', array('controller' => 'recurring_invoice', 'action' => 'edit'), array('recurring_profile_id' => Router::MATCH_ID));
      Router::map('recurring_profile_delete', 'recurring/profile/:recurring_profile_id/delete', array('controller' => 'recurring_invoice', 'action' => 'delete'), array('recurring_profile_id' => Router::MATCH_ID));

      Router::map('recurring_profile_trigger', 'recurring/profile/:recurring_profile_id/trigger', array('controller' => 'recurring_invoice', 'action' => 'trigger'), array('recurring_profile_id' => Router::MATCH_ID));
      Router::map('recurring_profile_duplicate', 'recurring/profile/:recurring_profile_id/duplicate', array('controller' => 'recurring_invoice', 'action' => 'duplicate'), array('recurring_profile_id' => Router::MATCH_ID));
      
      
      AngieApplication::getModule('environment')->defineStateRoutesFor('recurring_profile', 'recurring/profile/:recurring_profile_id', 'recurring_invoice', INVOICING_MODULE, array('recurring_profile_id' => Router::MATCH_ID));

	    // Recurring  Footprints
	    if (AngieApplication::isModuleLoaded('footprints')) {
		    AngieApplication::getModule('footprints')->defineAccessLogRoutesFor('recurring_profile', 'recurring/profile/:recurring_profile_id', 'recurring_invoice', INVOICING_MODULE, array('recurring_profile_id' => Router::MATCH_ID));
		    AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('recurring_profile', 'recurring/profile/:recurring_profile_id', 'recurring_invoice', INVOICING_MODULE, array('recurring_profile_id' => Router::MATCH_ID));
	    } // if

      // ---------------------------------------------------
      //  Invoicing settings
      // ---------------------------------------------------

      Router::map('invoicing_settings', 'invoicing/settings', array('controller' => 'invoicing_settings_admin'));
      Router::map('invoicing_settings_change_counter_value', 'invoicing/settings/change-counter-value', array('controller' => 'invoicing_settings_admin', 'action' => 'change_counter_value'));
      Router::map('invoicing_settings_change_description_formats', 'invoicing/settings/change-description-formats', array('controller' => 'invoicing_settings_admin', 'action' => 'change_description_formats'));

      Router::map('activity_logs_admin_rebuild_invoicing', 'admin/indices/activity-logs/rebuild/invoicing', array('controller' => 'activity_logs_admin', 'action' => 'rebuild_invoicing'));
      Router::map('object_contexts_admin_rebuild_invoicing', 'admin/indices/object-contexts/rebuild/invoicing', array('controller' => 'object_contexts_admin', 'action' => 'rebuild_invoicing'));
    } // defineRoutes

    /**
     * Define invoice routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */
    function defineInvoiceRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      $invoice_requirements = is_array($context_requirements) ? array_merge($context_requirements, array('invoicing_id' => Router::MATCH_ID)) : array('invoicing_id' => Router::MATCH_ID);
      
      Router::map("{$context}_invoicing", "$context_path/invoice/add", array('controller' => $controller_name, 'action' => "{$context}_add_invoice", 'module' => $module_name), $invoice_requirements);
      Router::map("{$context}_invoicing_preview_items", "$context_path/invoice/preview-items", array('controller' => $controller_name, 'action' => "{$context}_preview_items", 'module' => $module_name), $invoice_requirements);
    } // defineInvoiceRoutesFor
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_admin_panel', 'on_admin_panel');
      EventsManager::listen('on_main_menu', 'on_main_menu');
      EventsManager::listen('on_reports_panel', 'on_reports_panel');
      EventsManager::listen('on_custom_user_permissions', 'on_custom_user_permissions');
      EventsManager::listen('on_user_cleanup', 'on_user_cleanup');
      EventsManager::listen('on_object_deleted', 'on_object_deleted');
      EventsManager::listen('on_inline_tabs', 'on_inline_tabs');
      EventsManager::listen('on_invoices_tabs', 'on_invoices_tabs');
      EventsManager::listen('on_client_invoices_tabs', 'on_client_invoices_tabs');
      EventsManager::listen('on_object_inspector', 'on_object_inspector');
      EventsManager::listen('on_notification_inspector', 'on_notification_inspector');
      EventsManager::listen('on_tracking_report_options', 'on_tracking_report_options');
      EventsManager::listen('on_phone_homescreen', 'on_phone_homescreen');
      EventsManager::listen('on_projects_tabs', 'on_projects_tabs');
      EventsManager::listen('on_trash_sections', 'on_trash_sections');
      EventsManager::listen('on_trash_map', 'on_trash_map');
      EventsManager::listen('on_empty_trash', 'on_empty_trash');
      EventsManager::listen('on_object_options', 'on_object_options');

      // update objects that have anonymous client saved to People section
      EventsManager::listen('on_client_saved', 'on_client_saved');
      
      EventsManager::listen('on_context_domains', 'on_context_domains');
      EventsManager::listen('on_visible_contexts', 'on_visible_contexts');

      EventsManager::listen('on_project_created', 'on_project_created');
      EventsManager::listen('on_quick_add', 'on_quick_add');
     
      EventsManager::listen('on_daily', 'on_daily');
      
      EventsManager::listen('on_rebuild_activity_log_actions', 'on_rebuild_activity_log_actions');
      EventsManager::listen('on_activity_log_callbacks', 'on_activity_log_callbacks');
      
      EventsManager::listen('on_rebuild_object_contexts_actions', 'on_rebuild_object_contexts_actions');
      
      EventsManager::listen('on_object_from_notification_context', 'on_object_from_notification_context');
      EventsManager::listen('on_notification_context_view_url', 'on_notification_context_view_url');
      EventsManager::listen('on_extra_stats', 'on_extra_stats');

      EventsManager::listen('on_history_field_renderers', 'on_history_field_renderers');
    } // defineHandlers
    
    // ---------------------------------------------------
    //  Un(Install)
    // ---------------------------------------------------
    
    /**
     * Install this module
     * 
     * @param integer $position
     * @param boolean $bulk
     */
    function install($position = null, $bulk = false) {
      recursive_mkdir(WORK_PATH . '/invoices', 0777, WORK_PATH);
      
      parent::install($position, $bulk);
    } // install

    /**
     * Execute after module installation (through the interface)
     *
     * @param User $user
     */
    function postInstall($user) {
      $user->setSystemPermission('can_manage_finances', true, false);
      $user->setSystemPermission('can_manage_quotes', true, false);
      $user->save();
    } // postInstall
    
    /**
     * Uninstall this module
     */
    function uninstall() {
      parent::uninstall();
      
      $payments_table = TABLE_PREFIX . 'payments';
      if(DB::tableExists($payments_table)) {
        DB::execute("DELETE FROM $payments_table WHERE parent_type=?","Invoice");
      }//if
      delete_dir(WORK_PATH . '/invoices');

      Activitylogs::deleteByParentTypes(array('Invoice'));
    } // uninstall
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Invoicing');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @return string
     */
    function getDescription() {
      return lang('Adds invoicing support to activeCollab');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. Invoices created using this module will be deleted');
    } // getUninstallMessage

    /**
     * Return object types (class names) that this module is working with
     *
     * @return array
     */
    function getObjectTypes() {
      return array('Invoice', 'Quote', 'RecurringProfile');
    } // getObjectTypes
    
  }