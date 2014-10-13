<?php

  /**
   * Update activeCollab 2.3.3 to activeCollab 2.3.4
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0022 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '2.3.3';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '2.3.4';
    
    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateEmailTemplates' => 'Update email template for comments',
    	  'updateAssignmentFilters' => 'Update assignment filters',
    	);
    } // getActions
    
    /**
     * Update email template for Resources module
     *
     * @param void
     * @return boolean
     */
    function updateEmailTemplates() {
    	$all_templates = array(
    		'resources' => array(
	      	'new_comment' => array("[:project_name] New comment on ':object_name' :object_type has been posted", "<p>Hi,</p>\n
<p><a href=\":created_by_url\">:created_by_name</a> has replied to <a href=\":object_url\">:object_name</a> :object_type:</p>\n
<hr />\n
:comment_body\n
:details_body\n
<hr />\n
<p><a href=\":comment_url\">Click here</a> to see the comment.</p>\n
<p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'comment_body', 'comment_url', 'created_by_url', 'created_by_name', 'details_body'))
        )
      );

      $modules_table = TABLE_PREFIX . 'modules';
      $templates_table = TABLE_PREFIX . 'email_templates';
      $translations_table = TABLE_PREFIX . 'email_template_translations';

      foreach($all_templates as $module_name => $templates) {
        if(array_var(DB::executeFirstRow("SELECT COUNT(*) AS 'row_count' FROM $modules_table WHERE name = ?", $module_name), 'row_count')) {
          foreach($templates as $template_name => $template) {
            list($subject, $body, $variables) = $template;

            if(DB::executeFirstCell("SELECT COUNT(*) AS 'row_count' FROM $templates_table WHERE name = ? AND module = ?", $template_name, $module_name)) {
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
    
    /**
     * Update assignment filter options
     *
     * @return boolean
     */
    function updateAssignmentFilters() {
      $mysql_version = mysql_get_server_info();
      if($mysql_version && version_compare($mysql_version, '4.1', '>=')) {
        $collation = 'COLLATE utf8_general_ci';
        $charset = 'CHARACTER SET utf8';
      } else {
        $collation = '';
        $charset = '';
      } // if
      
      DB::execute("ALTER TABLE `".TABLE_PREFIX."assignment_filters` CHANGE `user_filter` `user_filter` ENUM('anybody','not_assigned','logged_user','logged_user_responsible','company','company_responsible','selected','selected_responsible') $charset $collation NOT NULL DEFAULT 'logged_user'");
      DB::execute("ALTER TABLE `".TABLE_PREFIX."assignment_filters`
                  ADD `tickets_only` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `status_filter`,
                  ADD `created_on_filter` ENUM( 'all', 'yesterday', 'last_week', 'last_month', 'today', 'this_week', 'this_month', 'selected_date', 'selected_range' ) $charset $collation NOT NULL DEFAULT 'all' AFTER `date_to`,
                  ADD `created_on_from` DATE NULL DEFAULT NULL AFTER `created_on_filter` ,
                  ADD `created_on_to` DATE NULL DEFAULT NULL AFTER `created_on_from`,
                  ADD `completed_on_filter` ENUM( 'all', 'yesterday', 'last_week', 'last_month', 'today', 'this_week', 'this_month', 'selected_date', 'selected_range' ) $charset $collation NOT NULL DEFAULT 'all' AFTER `created_on_to`,
                  ADD `completed_on_from` DATE NULL DEFAULT NULL AFTER `completed_on_filter` ,
                  ADD `completed_on_to` DATE NULL DEFAULT NULL AFTER `completed_on_from`");
      
      return true;
    } // updateAssignmentFilters
    
  }