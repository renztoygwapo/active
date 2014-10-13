{title}Email{/title}
{add_bread_crumb}Control Panel{/add_bread_crumb}


<div id="email_admin" class="wireframe_content_wrapper settings_panel">
  <div class="settings_panel_header">
    <table class="settings_panel_header_cell_wrapper two_cells">
      <tr>
        <td class="settings_panel_header_cell">
			      <h2>{lang}Outgoing Mail{/lang}</h2>
			      <div class="properties">
			        <div class="property" id="mailing_settings_mailing">
			          <div class="label">{lang}Engine{/lang}</div>
			          <div class="data"></div>
			        </div>
			        
			        <div class="property" id="mailing_settings_mailing_method">
			          <div class="label">{lang}Method{/lang}</div>
			          <div class="data"></div>
			        </div>
			        
			        <div class="property" id="mailing_settings_notifications_from">
			          <div class="label">{lang}From{/lang}</div>
			          <div class="data"></div>
			        </div>
			        
			        <div class="property" id="mailing_settings_queue">
			          <div class="label">{lang}Queue{/lang}</div>
			          <div class="data {if $queue_unsent}has_unsent{/if}">
			            {if $queue_total == 1}
			              <a href="{assemble route=outgoing_messages_admin}">{lang}One Message in Queue{/lang}</a> 
			            {else}
			              <a href="{assemble route=outgoing_messages_admin}">{lang total=$queue_total}:total Messages in Queue{/lang}</a> 
			            {/if}
			          {if $queue_unsent}
			            &mdash; <span class="error">{lang unsent=$queue_unsent}Failures: :unsent{/lang}</span>
			          {/if}
			          </div>
			        </div>
			      </div>

            {if !AngieApplication::isOnDemand()}
	          <ul class="settings_panel_header_cell_actions">
	            <li>{link href=Router::assemble('outgoing_email_admin_settings') mode=flyout_form success_event="mailing_settings_updated" title="Email Settings" class="link_button_alternative"}Change Settings{/link}</li>
	          </ul>
            {/if}
        </td>
        
        <td class="settings_panel_header_cell">
			      <h2>{lang}Incoming Mail{/lang}</h2>
			      <div class="properties">
			        <div class="property">
                {if AngieApplication::mailer()->isConnectionConfigurationLocked()}
                  <div class="label">{lang}Mailbox{/lang}</div>
                  <div class="data">{$smarty.const.MAILING_MESSAGE_FROM_EMAIL}</div>
                {else}
                  <div class="label">{lang}Mailboxes{/lang}</div>
                  <div class="data">{$mailbox_active} active of {$mailbox_total} defined</div>
                {/if}
              </div>
			        
			        <div class="property" id="incoming_mailing_settings_conflicts">
			          <div class="label">{lang}Conflicts{/lang}</div>
			          <div class="data">
			          	
			          	{if $conflict_total > 0}
			          		<a href="{assemble route=incoming_email_admin_conflict}">
			            		{if $conflict_total == 1}
			            			{lang}You have 1 conflict.{/lang}
			            		{else}
			            			{lang num=$conflict_total}You have :num conflicts.{/lang}
			            		{/if}
			            	</a>
			          	{else}
			          		{lang}No Conflicts{/lang}
			          	{/if}
			          </div>
			        </div>

              {if !AngieApplication::mailer()->isConnectionConfigurationLocked()}
                <div class="property">
                  <div class="label">{lang}Filters{/lang}</div>
                  <div class="data">{$filter_active} active of {$filter_total} defined</div>
                </div>
              {/if}
			      </div>
            
	          <ul class="settings_panel_header_cell_actions">
	          	<li>{link href=Router::assemble('incoming_email_admin_change_settings') mode=flyout_form success_event="incoming_mail_settings_updated" title="Incoming Email Settings" class="link_button_alternative"}Change Settings{/link}</li>
              {if !AngieApplication::mailer()->isConnectionConfigurationLocked()}
                <li>{link href=Router::assemble('incoming_email_admin_mailboxes') class="link_button_alternative"}Manage Mailboxes{/link}</li>
                <li>{link href=Router::assemble('incoming_email_admin_filters') class="link_button_alternative"}Manage Filters{/link}</li>
              {/if}
	          </ul>

        </td>
      
      </tr>
    </table>
  
  </div>
  
  <div class="settings_panel_body">
    {include file=get_view_path('_inline_tabs', null, $smarty.const.ENVIRONMENT_FRAMEWORK)}
  </div>
</div>

<script type="text/javascript">
  $('#email_admin').each(function() {
    var wrapper = $(this);

    var min_height = 100;
    wrapper.find('td.settings_panel_header_cell div.properties').each(function () { min_height = Math.max(min_height, $(this).height()); });
    wrapper.find('td.settings_panel_header_cell div.properties').css('min-height' , min_height + 'px');

    var inline_tabs = wrapper.find('div.inline_tabs:first');

    // when user clicks on to the link in properties, content will be loaded in inline tabs
    wrapper.find('#mailing_settings_queue a').click(function () {
      inline_tabs.find('#' + inline_tabs.attr('id') + '_outgoing_queue').click();
      return false;
    });

    // when user clicks on to the link in properties, content will be loaded in inline tabs
    wrapper.find('#incoming_mailing_settings_conflicts a').click(function () {
      inline_tabs.find('#' + inline_tabs.attr('id') + '_incoming_mail_conflicts').click();
      return false;    
    });  


    //  Incoming mail settings

    // On update
  	App.Wireframe.Events.bind('incoming_mail_settings_updated.content', function(e, response) {
  	  App.Wireframe.Flash.success(App.lang('Incoming Mail settings updated'));
  	});

    /**
   	 * Update settings display
   	 * 
   	 *  @param String mailing
   	 *  @param String smtp_host
   	 *  @param String smtp_port
   	 *  @param String method
   	 *  @param String from_name
   	 *  @param String from_email
     */
    var update_settings_display = function(mailing, smtp_host, smtp_port, method, from_name, from_email) {
      switch(mailing) {
      	case 'disabled':
        	wrapper.find('#mailing_settings_mailing div.data').text(App.lang('Disabled'));
        	break;
      	case 'silent':
      	  wrapper.find('#mailing_settings_mailing div.data').text(App.lang('Silent'));
        	break;
      	case 'native':
      	  wrapper.find('#mailing_settings_mailing div.data').text(App.lang('Native'));
        	break;
      	case 'smtp':
      	  wrapper.find('#mailing_settings_mailing div.data').text(App.lang('SMTP (:host::port)', {
        	  'host' : smtp_host, 
        	  'port' : smtp_port
        	}));
        	break;
        default:
          wrapper.find('#mailing_settings_mailing div.data').text(App.lang('Invalid Option Value'));
      } // switch

      if(method == 'in_background') {
        wrapper.find('#mailing_settings_mailing_method div.data').text(App.lang('Send Messages in Background'));
      } else {
        wrapper.find('#mailing_settings_mailing_method div.data').text(App.lang('Send Instantly'));
      } // if

      if(typeof(from_name) == 'string' && from_name && typeof('from_email') == 'string' && from_email && from_name != from_email) {
        wrapper.find('#mailing_settings_notifications_from div.data').html(App.clean(from_name) + ' &lt;' + App.clean(from_email) + '&gt;');
      } else if(typeof('from_email') == 'string' && from_email) {
        wrapper.find('#mailing_settings_notifications_from div.data').html(App.clean(from_email));
      } else {
        wrapper.find('#mailing_settings_notifications_from div.data').html('<span class="details">' + App.lang('-- Not Set --') + '</span>');
      } // if
    }; // update_settings_display

    // Initial values
    update_settings_display({$mailing|json nofilter}, {$smtp_host|json nofilter}, {$smtp_port|json nofilter}, {$mailing_method|json nofilter}, {$from_name|json nofilter}, {$from_email|json nofilter});

    
  	// On update
  	App.Wireframe.Events.bind('mailing_settings_updated.content', function(e, response) {
  	  if(typeof(response) == 'object') {
  			var mailing = response['mailing'];
  			var method = response['mailing_method'];
  			
  			var from_email = typeof(response['notifications_from_email']) == 'string' && response['notifications_from_email'] ? response['notifications_from_email'] : '';
  			var from_name = typeof(response['notifications_from_name']) == 'string' && response['notifications_from_name'] ? response['notifications_from_name'] : '';

  			if(mailing == 'smtp') {
  				var smtp_host = response['mailing_smtp_host'];
  				var smtp_port = response['mailing_smtp_port'];
  			} // if
  		} else {
  		  var mailing = null, method = null;
  			var from_name = '', from_email = '';
  		} // if

  		if(typeof(smtp_host) == 'undefined') {
  			var smtp_host = '';
  		} // if

  		if(typeof(smtp_port) == 'undefined') {
  			var smtp_port = '';
  		} // if
  		
  		update_settings_display(mailing, smtp_host, smtp_port, method, from_name, from_email);
  	});

		//update incoming mail conflict number
  	App.Wireframe.Events.bind('incoming_mail_conflict_deleted.content incoming_mail_conflict_resolved.content', function(e, response) {
  		refresh_conflict_number(response.conflicts);
    });

  	App.Wireframe.Events.bind('refresh_conflict_number.content', function(e, conflict_number) {
  		refresh_conflict_number(conflict_number);
    });

  	
  	var refresh_conflict_number = function(conflict_number) {
			
			var incoming_mailing_settings_conflicts = wrapper.find('#incoming_mailing_settings_conflicts .data').empty();

			if(conflict_number == 0) {
	  			incoming_mailing_settings_conflicts.append(App.lang('No Conflicts'));
	  	} else {
	  	  	var link = $('<a href="{assemble route=incoming_email_admin_conflict}"></a>');
					if(conflict_number == 1) {
	  				link.append(App.lang('You have 1 conflict.'));
	  			} else {
		  			var text_link = App.lang('You have :num conflicts.', {
			  			'num' : conflict_number
			  			});
						link.append(text_link);
					}//if

					 // when user clicks on to the link in properties, content will be loaded in inline tabs
					link.click(function () {
						inline_tabs.find('#' + inline_tabs.attr('id') + '_incoming_mail_conflicts').click();
			      return false;    
			    });  
					incoming_mailing_settings_conflicts.append(link);
		  }//if
		};//refresh_conflict_number
  	
  	
  });
</script>
