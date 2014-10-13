{title}General Settings{/title}
{add_bread_crumb}General Settings{/add_bread_crumb}

<div id="general_settings">
  {form action=Router::assemble('admin_settings_general') method=post}
    <div class="content_stack_wrapper">

      {if !AngieApplication::isOnDemand()}
        <div class="content_stack_element">
          <div class="content_stack_element_info">
            <h3>{lang}Version Checking{/lang}</h3>
          </div>
          <div class="content_stack_element_body">
            {wrap field=help_improve_application}
              {yes_no name=help_improve_application value=$general_data.help_improve_application label='Help Us Improve activeCollab'}
              <p class="aid">{lang}Select "Yes" to help us improve activeCollab by <u>sending anonymous, non-identifying usage information</u> when activeCollab checks for a new version. This information is used by our development team to make better decisions while working on future releases.{/lang}</p>
            {/wrap}
          </div>
        </div>
      {/if}

      {if AngieApplication::isModuleLoaded('tracking')}
        <div class="content_stack_element">
          <div class="content_stack_element_info">
            <h3>{lang}Time and Expenses{/lang}</h3>
          </div>
          <div class="content_stack_element_body">
            {wrap field=default_billable_status}
              {label}Default Billable Status for New Entries{/label}
              <div>{radio_field name='default_billable_status' value=0 pre_selected_value=$general_data.default_billable_status label='Non-Billable'}</div>
              <div>{radio_field name='default_billable_status' value=1 pre_selected_value=$general_data.default_billable_status label='Billable'}</div>
            {/wrap}
          </div>
        </div>
      {/if}

      {if !AngieApplication::isOnDemand()}
        <div class="content_stack_element last">
          <div class="content_stack_element_info">
            <h3>{lang}Miscellaneous{/lang}</h3>
            <p class="aid">{lang}Various settings and features{/lang}</p>
          </div>
          <div class="content_stack_element_body">
            {wrap field=on_logout_url}
              {label}When Users Log Out{/label}
              <div><input type="radio" name="use_on_logout_url" class="auto input_radio" value="0" id="generalUseLogoutUrlNo" {if !$general_data.use_on_logout_url}checked="checked"{/if} /> {label for=generalUseLogoutUrlNo class=inline main_label=false after_text=''}Redirect them back to login page{/label}</div>
              <div><input type="radio" name="use_on_logout_url" class="auto input_radio" value="1" id="generalUseLogoutUrlYes" {if $general_data.use_on_logout_url}checked="checked"{/if} /> {label for=generalUseLogoutUrlYes class=inline main_label=false after_text=''}Redirect them to a custom URL{/label}</div>
              <div id="on_logout_url_container">
                {text_field name="general[on_logout_url]" value=$general_data.on_logout_url id=on_logout_url}
                <p class="details block">{lang}Specify URL users will be redirected to when they log out{/lang}</p>
              </div>
            {/wrap}
          </div>
        </div>
      {/if}

    </div>
    
    {wrap_buttons}
  	  {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $('#general_settings').each(function() {
    var wrapper = $(this);
    var on_logout_url = wrapper.find('#on_logout_url_container');

    if(wrapper.find('#generalUseLogoutUrlNo').prop('checked')) {
      on_logout_url.hide();
    } // if

    wrapper.find('#generalUseLogoutUrlNo').click(function() {
      on_logout_url.slideUp('fast');
    });

    wrapper.find('#generalUseLogoutUrlYes').click(function() {
      on_logout_url.slideDown('fast', function() {
        $(this).find('#on_logout_url').focus();
      });
    });
  });
</script>