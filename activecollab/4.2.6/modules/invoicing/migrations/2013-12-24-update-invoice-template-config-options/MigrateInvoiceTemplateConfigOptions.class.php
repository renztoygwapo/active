<?php

  /**
   * Class MigrateInvoiceTemplateConfigOptions
   *
   * Set default values for Invoice Template config options
   *
   * @package activecollab.modules.invoicing
   */
  class MigrateInvoiceTemplateConfigOptions extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      $config_option_name = 'invoice_template';

      $config_option_value = $this->getConfigOptionValue($config_option_name);

      if(is_array($config_option_value)) {
        $config_option_value['show_amount_paid_balance_due'] = true;
        $this->setConfigOptionValue($config_option_name, $config_option_value);
      } // if
    } // up
    
  }