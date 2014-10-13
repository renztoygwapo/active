<?php

  // Build on top of backend controller
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);
  
  /**
   * Invoicing settings controller
   * 
   * @package application.modules.invoicing
   * @subpackage controllers
   */
  class InvoicingSettingsAdminController extends AdminController {
    
    /**
     * Index page
     */
    function index() {
      if($this->request->isAsyncCall()) {
        list($total_counter, $year_counter, $month_counter) = Invoices::getDateInvoiceCounters();
        
        $total_counter++; 
        $year_counter++; 
        $month_counter++;
        
        $this->response->assign(array(
          'settings_data' => ConfigOptions::getValue(array(
            'print_invoices_as',
            'print_proforma_invoices_as',
            'on_invoice_based_on',
            'invoicing_number_pattern',
            'invoicing_number_counter_padding',
            'invoicing_default_due',
            'invoice_notify_on_payment',
            'invoice_notify_on_cancel',
            'invoice_notify_financial_manager_ids',
            'invoice_notify_financial_managers',
            'invoice_second_tax_is_enabled',
            'invoice_second_tax_is_compound'
          )),
          'description_formats' => array(
            'description_format_grouped_by_task' => array(
              'text' => lang('When Records are Grouped by Task'),
              'format' => ConfigOptions::getValue('description_format_grouped_by_task'),
            ),
            'description_format_grouped_by_project' => array(
              'text' => lang('When Records are Grouped by Project'),
              'format' => ConfigOptions::getValue('description_format_grouped_by_project'),
            ),
            'description_format_grouped_by_job_type' => array(
              'text' => lang('When Records are Grouped by Job Type'),
              'format' => ConfigOptions::getValue('description_format_grouped_by_job_type'),
            ),
            'description_format_separate_items' => array(
              'text' => lang('When Records are Displayed as Individual Invoice Items'),
              'format' => ConfigOptions::getValue('description_format_separate_items'),
            ),
          ),
          'pattern_variables' => array(
            INVOICE_VARIABLE_CURRENT_YEAR => date('Y'),
            INVOICE_VARIABLE_CURRENT_MONTH => date('n'),
            INVOICE_VARIABLE_CURRENT_MONTH_SHORT => date('M'),
            INVOICE_VARIABLE_CURRENT_MONTH_LONG => date('F'),
            INVOICE_NUMBER_COUNTER_TOTAL => $total_counter,
            INVOICE_NUMBER_COUNTER_YEAR => $year_counter,
            INVOICE_NUMBER_COUNTER_MONTH => $month_counter
          ), 
          'counters' => array('total_counter' => $total_counter, 'year_counter' => $year_counter, 'month_counter' => $month_counter),
          'change_counter_value_url' => Router::assemble('invoicing_settings_change_counter_value')
        ));
        
        if($this->request->isSubmitted()) {
          $settings = $this->request->post('settings');
          
          try {
            if(isset($settings['invoicing_number_pattern']) && $settings['invoicing_number_pattern']) {
              ConfigOptions::setValue('invoicing_number_pattern', $settings['invoicing_number_pattern']);
              ConfigOptions::setValue('invoicing_number_counter_padding', (integer) $settings['invoicing_number_counter_padding']);

              if((strpos($settings['invoicing_number_pattern'], INVOICE_NUMBER_COUNTER_TOTAL) === false) && (strpos($settings['invoicing_number_pattern'], INVOICE_NUMBER_COUNTER_YEAR) === false) && (strpos($settings['invoicing_number_pattern'], INVOICE_NUMBER_COUNTER_MONTH) === false)) {
                throw new ValidationErrors(array(
                  'invoicing_number_pattern' => lang('One of invoice counters is required (:total, :year, :month)', array('total' => INVOICE_NUMBER_COUNTER_TOTAL,'year' => INVOICE_NUMBER_COUNTER_YEAR,'month' => INVOICE_NUMBER_COUNTER_MONTH)), 
                ));
              } // if
            } else {
              throw new ValidationErrors(array(
                'invoicing_number_pattern' => lang('Number generator pattern is required'), 
              ));
            } // if

            ConfigOptions::setValue('print_invoices_as', (isset($settings['print_invoices_as']) && trim($settings['print_invoices_as']) ? trim($settings['print_invoices_as']) : null));
            ConfigOptions::setValue('print_proforma_invoices_as', (isset($settings['print_proforma_invoices_as']) && trim($settings['print_proforma_invoices_as']) ? trim($settings['print_proforma_invoices_as']) : null));
            ConfigOptions::setValue('on_invoice_based_on', $settings['on_invoice_based_on']);

            // notify options
            ConfigOptions::setValue('invoice_notify_on_payment', (integer) $settings['invoice_notify_on_payment']);
            ConfigOptions::setValue('invoice_notify_on_cancel', (integer) $settings['invoice_notify_on_cancel']);

            // tax options
            ConfigOptions::setValue('invoice_second_tax_is_enabled', (integer) $settings['invoice_second_tax_is_enabled']);
            ConfigOptions::setValue('invoice_second_tax_is_compound', (integer) $settings['invoice_second_tax_is_compound']);
            
            //notify these financial managers
            ConfigOptions::setValue('invoice_notify_financial_managers',  $settings['invoice_notify_financial_managers']);
            ConfigOptions::setValue('invoice_notify_financial_manager_ids',  $settings['invoice_notify_financial_manager_ids']);
            
            if(array_key_exists('invoicing_default_due', $settings)) {
              $invoicing_default_due = (integer) $settings['invoicing_default_due'];

              if($invoicing_default_due < 0) {
                $invoicing_default_due = 0;
              } // if

              ConfigOptions::setValue('invoicing_default_due', $invoicing_default_due);
            } // if
            
            $this->response->ok();
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // index
    
    /**
     * Change counter value
     */
    function change_counter_value() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        $counter_type = $this->request->post('counter_type');
        $counter_value = (integer) $this->request->post('counter_value');
        
        if($counter_value < 1) {
          $this->response->badRequest();
        } // if
        
        if($counter_type == 'total_counter' || $counter_type == 'month_counter' || $counter_type == 'year_counter') {
          try {
            list($total_counter, $year_counter, $month_counter) = Invoices::getDateInvoiceCounters();
          
            switch($counter_type) {
              case 'total_counter':
                $total_counter = $counter_value - 1;
                break;
              case 'month_counter':
                $month_counter = $counter_value - 1;
                break;
              case 'year_counter':
                $year_counter = $counter_value - 1;
                break;
            } // switch
            
            Invoices::setDateInvoiceCounters($total_counter, $year_counter, $month_counter);
            $this->response->respondWithData($counter_value);
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } else {
          $this->response->badRequest();
        } // if
        
        switch($counter_type) {
          case INVOICE_NUMBER_COUNTER_TOTAL:
          case INVOICE_NUMBER_COUNTER_MONTH:
          case INVOICE_NUMBER_COUNTER_YEAR:
            
            break;
          default:
            $this->response->notFound();
        } // switch
      } else {
        $this->response->badRequest();
      } // if
    } // change_counter_value

    /**
     * Change description formats
     */
    function change_description_formats() {
      if($this->request->isAsyncCall()) {
        $config_option_names = array(
          'description_format_grouped_by_task',
          'description_format_grouped_by_project',
          'description_format_grouped_by_job_type',
          'description_format_separate_items',
          'first_record_summary_transformation',
          'second_record_summary_transformation',
        );

        $formats_data = $this->request->post('formats', ConfigOptions::getValue($config_option_names));

        $this->response->assign('formats_data', $formats_data);

        if($this->request->isSubmitted()) {
          DB::transact(function() use ($config_option_names, $formats_data) {
            foreach($config_option_names as $config_option_name) {
              if(array_key_exists($config_option_name, $formats_data)) {
                ConfigOptions::setValue($config_option_name, trim($formats_data[$config_option_name]));
              } // if
            } // foreach
          });

          $this->response->respondWithData(ConfigOptions::getValue($config_option_names), array(
            'as' => 'fromats',
          ));
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // change_description_formats
    
  }