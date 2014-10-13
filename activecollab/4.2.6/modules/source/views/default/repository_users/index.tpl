{title}Manage Repository Users{/title}
{use_widget name="form" module="environment"}

<div class="fields_wrapper">
	<div id="repository_users">
  
    <h3 id="no_users">{lang}There are no commits in the repository and repository users can't be mapped yet{/lang}.</h3>
  
		<div class="" style="display: none">
	    <h3 id="map_users">{lang}Map repository users with activeCollab people added to this project{/lang}</h3>
		  <h3 id="all_mapped">{lang}All repository users are mapped with activeCollab users{/lang}.</h3>  
		</div>
  
	  {form id=map_user_form action=$add_mapping_url method=post}
		  <table id="records" class="common mapped_users" cellspacing="0">
		    <thead>
		      <tr>
		        <th>{lang}Repository User{/lang}</th>
		        <th>{lang}activeCollab User{/lang}</th>
		        <th class="options">{lang}Options{/lang}</th>
		      </tr>
		    </thead>
        
		    <tbody>
			    {if is_foreachable($source_users)}
				    {foreach from=$source_users item=source_user name=source_foreach}
				      <tr class="mapped_users_list">
				        <td class="repository_user">{$source_user->getRepositoryUser()}</td>
				        <td class="user">{user_link user=$source_user->system_user}</td>
				        <td class="options">
				        	<a href="{$source_user->getDeleteUrl($active_project)}" title="Remove this mapping" name="{$source_user->getRepositoryUser()}" class="remove_source_user">
				        		<img src='{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}' alt='' />
				        	</a>
				        </td>
				      </tr>
				    {/foreach}
			    {/if}
		    </tbody>
        
        <tfoot>
          <tr>
            <td>
		          <select id="repository_user" name="repository_user">
		          {foreach from=$repository_users item=repository_user}
		            <option value="{$repository_user}">{$repository_user}</option>
		          {/foreach}
		          </select>
            </td>
            <td>
              {if $active_project instanceof Project}
                {select_project_user id=user_id name=user_id project=$active_project user=$logged_user}
              {else}
                {select_user id=user_id name=user_id user=$logged_user}
              {/if}
            </td>
            <td class="options">{submit id='submit_map'}Map{/submit}</td>
          </tr>
        </tfoot>
		  </table>
	  {/form}
	</div>
  
  <script type="text/javascript">
    var map_user_form = $('#repository_users form');
    
	  var new_mapping_form = $('#records tfoot');
	  var currently_mapped_users = $('#records tbody');
	
	  var map_users_message = $('#map_users');
	  var all_mapped_message = $('#all_mapped');
	  var no_users_message = $('#no_users');
	  
	  var repository_users = $('#repository_user');
	  var project_users = $('#user_id');

    var delete_image_url = '{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}';
    {literal}


		/**
     * Refresh form and all messages
     *
     * @param void
     * @return null
     */
	  var refreshForm = function () {
	    all_mapped_message.hide();
	    no_users_message.hide();
	    map_users_message.hide();
      
      new_mapping_form.hide();
      currently_mapped_users.hide();

      var repository_users_count = repository_users.find('option').length;
      var currently_mapped_users_count = currently_mapped_users.find('tr').length;

      if (!currently_mapped_users_count && !repository_users_count) {
        // there are no repository users, and there are no mappings, so repository is not updated
        no_users_message.show();
      } else {
	      if (currently_mapped_users_count > 0) {
	        currently_mapped_users.show();
	      } // if

	      if (repository_users_count > 0) {
	 	      // if all users are not added show the form
	        new_mapping_form.show();
	        map_users_message.show();
	      } else {
          // otherwise show the message
	        all_mapped_message.show();
	      } // if 
      } // if      
	  }; // refreshForm
    refreshForm();

    // submit add form via ajax
    map_user_form.ajaxForm({
      url : map_user_form.attr('action'),
      beforeSubmit : function () {
        new_mapping_form.find('button, select').attr('disabled', true);        
      },
      success : function (response) {
        var new_mapping = '<tr>';
        new_mapping += '<td class="repository_user">' + App.clean(response.repository_user) + '</td>';
        new_mapping += '<td class="user"><a href="' + response.user.permalink + '">' + App.clean(response.user.display_name) + '</a></td>';
        new_mapping += '<td class="options"><a href="' + response.urls.delete + '" title="Remove this mapping" name="' + response.repository_user + '" class="remove_source_user"><img src="' + delete_image_url + '" alt="" /></a></td>';
        new_mapping += '</tr>';
        new_mapping = $(new_mapping).appendTo(currently_mapped_users);

        repository_users.find('option[value="' + response.repository_user + '"]').remove();
        new_mapping_form.find('button, select').attr('disabled', false);

        refreshForm();
      },
      error : function (response, response_text) {
        App.Wireframe.Flash.error(App.lang('Failed to add mapping'));
        new_mapping_form.find('button, select').attr('disabled', false);
      }
    });

    currently_mapped_users.click(function (event) {
      var target = $(event.target);
      
      if (!target.is('.remove_source_user')) {
        target = target.parents('.remove_source_user:first');
      } // if

      if (target.is('.remove_source_user')) {
        return delete_mapping(target);
      } // if
    });

    /**
     * Delete existing mapping
     *
     * @param jQuery mapping
     * @return null
     */
    var delete_mapping = function (mapping) {
      if (mapping.is('.processing')) {
        return false;
      } // if
      
      var mapping_row = mapping.parents('tr:first');
      var repository_user = mapping.attr('name');
      var mapping_icon = mapping.find('img:first');

      mapping.addClass('processing');
      mapping_icon.attr('src', App.Wireframe.Utils.indicatorUrl('small'));

      $.ajax({
        url : mapping.attr('href'),
        data : {submitted : 'submitted', repository_user : repository_user},
        type : 'post', 
        success : function (response) {
          mapping_row.remove();
          $('<option value="' + App.clean(repository_user) + '">' + App.clean(repository_user) + '</option>').appendTo(repository_users);
          refreshForm();
        },
        error : function (response, response_text) {
          mapping_icon.attr('src', delete_image_url);
          mapping.removeClass('processing');
          App.Wireframe.Flash.error(App.lang('Failed to remove mapping'));
        }
      });
      
      return false;
    } // mapping

    {/literal}
	</script>
</div>

