{title}Task Settings{/title}
{add_bread_crumb}Settings{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

<div id="tasks_admin" class="wireframe_content_wrapper settings_panel">
  <div class="settings_panel_header">
    <table class="settings_panel_header_cell_wrapper">
      <tr>
        <td class="settings_panel_header_cell">
          <h2>{lang}Task Settings{/lang}</h2>
		      <div class="properties">
		        <div class="property" id="tasks_setting_auto_reopen">
		          <div class="label">{lang}Auto-Reopen{/lang}</div>
		          <div class="data"></div>
		        </div>
		        
		        <div class="property" id="tasks_setting_public_forms_enabled">
		          <div class="label">{lang}Public Forms{/lang}</div>
		          <div class="data"></div>
		        </div>
		        
		        <div class="property" id="tasks_setting_use_captcha">
		          <div class="label">{lang}CAPTCHA{/lang}</div>
		          <div class="data"></div>
		        </div>

            <div class="property" id="tasks_setting_custom_fields">
              <div class="label">{lang}Custom Fields{/lang}</div>
              <div class="data"></div>
            </div>
		      </div>
          
          <ul class="settings_panel_header_cell_actions">
            <li>{link href=Router::assemble('tasks_admin_settings') mode=flyout_form title="Change Settings" success_event=tasks_settings_updated class="link_button_alternative"}Change Settings{/link}</li>
          </ul>
        </td>
      </tr>
    </table>
  </div>
  
  <div class="settings_panel_body">
    <div id="public_task_forms"></div>
  </div>
</div>

<script type="text/javascript">
  $('#tasks_admin').each(function() {
    var wrapper = $(this);

    /**
     * Update messages in settings block
     *
     * @param Boolean auto_reopen
     * @param Boolean auto_reopen_clients_only
     * @param Boolean public_submit_enabled
     * @param Boolean use_captcha
     * @param Array custom_fields
     */
    var update_settings_display = function(auto_reopen, auto_reopen_clients_only, public_submit_enabled, use_captcha, custom_fields) {
      if(auto_reopen) {
        if(auto_reopen_clients_only) {
          $('#tasks_setting_auto_reopen div.data').text(App.lang('Enabled. Completed tasks will be reopened when user who is not a member of :owner company posts a comment', {
            'owner' : {$owner_company->getName()|json nofilter}
          }));
        } else {
          $('#tasks_setting_auto_reopen div.data').text(App.lang('Enabled. Completed tasks will be reopened when new comment is posted'));
        } // if
      } else {
        $('#tasks_setting_auto_reopen div.data').text(App.lang('Disabled. Completed tasks will not be reopened on new comments'));
      } // if

      if(public_submit_enabled) {
        $('#tasks_setting_public_forms_enabled div.data').text(App.lang('Enabled. Visitors will be able to create new tasks through public forms'));
        $('#tasks_setting_use_captcha').show().find('div.data').text(use_captcha ?
          App.lang('Public tasks forms are protected with CAPTCHA images') :
          App.lang('Public tasks forms are not protected with CAPTCHA images')
        );
      } else {
        $('#tasks_setting_public_forms_enabled div.data').text(App.lang('Disabled. Users will not be able to post new tasks without logging in'));
        $('#tasks_setting_use_captcha').hide();
      } // if

      if(jQuery.isArray(custom_fields) && custom_fields.length) {
        var cleared_values = [];

        App.each(custom_fields, function(k, v) {
          cleared_values.push(App.clean(v));
        });

        wrapper.find('#tasks_setting_custom_fields div.data').empty().append(cleared_values.join(', '));
      } else {
        wrapper.find('#tasks_setting_custom_fields div.data').empty().append(App.lang('Custom Fields are not Configured'));
      } // if
    }; // update_settings_display

    // Initial value
    update_settings_display({$tasks_auto_reopen|json nofilter}, {$tasks_auto_reopen_clients_only|json nofilter}, {$tasks_public_submit_enabled|json nofilter}, {$tasks_use_captcha|json nofilter}, {$task_custom_fields|json nofilter});

    // On update
    App.Wireframe.Events.bind('tasks_settings_updated.content', function(e, response) {
      if(typeof(response) == 'object') {
        var auto_reopen = response['tasks_auto_reopen'];
        var auto_reopen_clients_only = auto_reopen && response['tasks_auto_reopen_clients_only'];
        var public_submit_enabled = response['tasks_public_submit_enabled'];
        var use_captcha = public_submit_enabled && response['tasks_use_captcha'];
        var custom_fields = typeof(response['task_custom_fields']) != 'undefined' && jQuery.isArray(response['task_custom_fields']) ? response['task_custom_fields'] : [];
      } else {
        var auto_reopen = false, auto_reopen_clients_only = false, public_submit_enabled = false, use_captcha = false; custom_fields = false;
      } // if

      update_settings_display(auto_reopen, auto_reopen_clients_only, public_submit_enabled, use_captcha, custom_fields);
    });

    // Forms
    wrapper.find('#public_task_forms').pagedObjectsList({
      'load_more_url' : '{assemble route=tasks_admin}',
      'items' : {$forms|json nofilter},
      'items_per_load' : {$forms_per_page},
      'total_items' : {$total_forms},
      'list_items_are' : 'tr',
      'list_item_attributes' : { 'class' : 'task_form' },
      'columns' : {
        'is_enabled' : '',
        'name' : App.lang('Form'),
        'project' : App.lang('Project'),
        'options' : ''
      },
      'sort_by' : function() {
        return $(this).find('td.name span.form_name').text();
      },
      'empty_message' : App.lang('There are no public task forms defined'),
      'listen' : 'public_task_form',
      'on_add_item' : function(item) {
        var form = $(this);

        form.append(
          '<td class="is_enabled"></td>' +
            '<td class="name"></td>' +
            '<td class="project"></td>' +
            '<td class="options"></td>'
        );

        form.attr('id', item['id']);

        var checkbox = $('<input type="checkbox" />').attr({
          'on_url' : item['urls']['enable'],
          'off_url' : item['urls']['disable']
        }).asyncCheckbox({
            'success_event' : 'public_task_form_updated',
            'success_message' : [ App.lang('Form has been disabled'), App.lang('Form has been enabled') ]
          }).appendTo(form.find('td.is_enabled'));

        if(item['is_enabled']) {
          checkbox[0].checked = true;
        } // if

        form.find('td.name').html('<span class="form_name">' + App.clean(item['name']) + '</span>' + '<a href="' + App.clean(item['urls']['public']) + '" class="form_url" target="_blank">' + App.clean(item['urls']['public']) + '</a>');
        $('<a></a>').attr('href', item['project']['url']).text(item['project']['name']).appendTo(form.find('td.project'));

        form.find('td.options')
          .append('<a href="' + item['urls']['edit'] + '" class="edit_form" title="' + App.lang('Change Settings') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Edit') + '" /></a>')
          .append('<a href="' + item['urls']['delete'] + '" class="delete_form" title="' + App.lang('Remove Form') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Delete') + '" /></a>')
        ;

        form.find('td.options a.edit_form').flyoutForm({
          'success_event' : 'public_task_form_updated'
        });
        form.find('td.options a.delete_form').asyncLink({
          'confirmation' : App.lang('Are you sure that you want to permanently delete this public task form?'),
          'success_event' : 'public_task_form_deleted',
          'success_message' : App.lang('Public task form has been deleted successfully')
        });
      }
    });
  });
</script>