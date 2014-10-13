<?php

  /**
   * Update activeCollab 2.3.2 to activeCollab 2.3.3
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0021 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '2.3.2';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '2.3.3';
    
    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateEmailTemplates' => 'Update Documents module email templates', 
    	);
    } // getActions
    
    /**
     * Update email template for Documents module
     *
     * @param void
     * @return boolean
     */
    function updateEmailTemplates() {
    	$all_templates = array(
    		'documents' => array(
	      	'new_text_document' => array("New text document ':document_name' has been created", "<p>Hi,</p>\n
	<p><a href=\":created_by_url\">:created_by_name</a> has created a new text document.</p>\n
	<p><a href=\":document_url\">Click here</a> for more details.</p>\n
	<p>Best,<br />:owner_company_name</p>", array('document_name', 'document_url', 'created_by_name', 'created_by_url', 'owner_company_name')),
	
	      	'new_file_document' => array("New file document ':document_name' has been uploaded", "<p>Hi,</p>\n
	<p><a href=\":created_by_url\">:created_by_name</a> has uploaded a new file document.</p>\n
	<p><a href=\":document_url\">Click here</a> for more details.</p>\n
	<p>Best,<br />:owner_company_name</p>", array('document_name', 'document_url', 'created_by_name', 'created_by_url', 'owner_company_name')),
      	)
      );

      $modules_table = TABLE_PREFIX . 'modules';
      $templates_table = TABLE_PREFIX . 'email_templates';
      $translations_table = TABLE_PREFIX . 'email_template_translations';

      foreach($all_templates as $module_name => $templates) {
        if(array_var(DB::executeFirstRow("SELECT COUNT(*) AS 'row_count' FROM $modules_table WHERE name = ?", array($module_name)), 'row_count')) {
          foreach($templates as $template_name => $template) {
            list($subject, $body, $variables) = $template;

            if(array_var(DB::executeFirstRow("SELECT COUNT(*) AS 'row_count' FROM $templates_table WHERE name = ? AND module = ?", $template_name, $module_name), 'row_count')) {
              DB::execute("UPDATE $templates_table SET subject = ?, body = ?, variables = ? WHERE name = ? AND module = ?", $subject, $body, implode("\n", $variables), $template_name, $module_name);
              DB::execute("DELETE FROM $translations_table WHERE name = ? AND module = ?", $template_name, $module_name);
            } else {
              DB::execute("INSERT INTO $templates_table (name, module, subject, body, variables) VALUES (?, ?, ?, ?, ?)", $template_name, $module_name, $subject, $body, implode("\n", $variables));
            } // if
          } // foreach
        } // if
      } // foreach

      return true;
    } // updateEmailTemplates
    
  }