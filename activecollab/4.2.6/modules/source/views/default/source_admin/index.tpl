{title}Source Repositories{/title}
{add_bread_crumb}Control Panel{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

<div id="source_admin" class="wireframe_content_wrapper settings_panel">
  <div class="settings_panel_header">
    <table class="settings_panel_header_cell_wrapper two_cells">
      <tr>
        <td class="settings_panel_header_cell">
			    <h2>{lang}Subversion{/lang}</h2>
			  	<div class="properties">
			  	  <div class="property" id="svn_engine">
			  	    <div class="label">{lang}Engine{/lang}</div>
			        <div class="data">{RepositoryEngine::getName()}</div>
			  	  </div>
			  	  <div class="property" id="svn_exec_path">
			        <div class="label">{lang}Executable Path{/lang}</div>
			        <div class="data">{$source_data.svn_path}</div>
			  	  </div>
			  	  <div class="property" id="svn_config_path">
			  	    <div class="label">{lang}Config Path{/lang}</div>
			        <div class="data">
              {if $source_data.svn_config_dir}
                {$source_data.svn_config_dir}
              {else}
                <span class="details">{lang}Default{/lang}</span>
              {/if}
			        </div>
			  	  </div>
            <div class="property" id="svn_trust_server_cert">
              <div class="label">{lang}Trust Certificate{/lang}</div>
              <div class="data">
                {if $source_data.svn_trust_server_cert}
                  {lang}Yes{/lang}
                {else}
                  {lang}No{/lang}
                {/if}
              </div>
            </div>
			    </div>
          <ul class="settings_panel_header_cell_actions">
            <li>{link href=Router::assemble('admin_source_svn_settings') async=true mode='flyout_form' title='Subversion Settings' success_event='svn_updated' class="link_button_alternative"}Change Subversion Settings{/link}</li>
          </ul>
        </td>
        <td class="settings_panel_header_cell">
          <h2>{lang}Mercurial{/lang}</h2>
          <div class="properties">
			      <div class="property" id="mercurial_exec_path">
			        <div class="label">{lang}Mercurial Engine{/lang}</div>
			        <div class="data">{$source_data.mercurial_path}</div>
			      </div>
          </div>
          <ul class="settings_panel_header_cell_actions">
		  	   <li>{link href=Router::assemble('admin_source_mercurial_settings') async=true mode='flyout_form' title='Mercurial Settings' success_event='mercurial_updated' class="link_button_alternative"}Change Mercurial Settings{/link}</li>
          </ul>
        </td>
      </tr>
    </table>
  </div>
  
  <div class="settings_panel_body" id="repositories"></div>
</div>

<script type="text/javascript">

	var wrapper = $('#source_admin');
	var min_height = 0;
	wrapper.find('td.settings_panel_header_cell div.properties').each(function () { min_height = Math.max(min_height, $(this).height()); });
	wrapper.find('td.settings_panel_header_cell div.properties').css('min-height' , min_height + 'px');
	
	App.Wireframe.Events.bind('svn_updated.content', function(event, svn_source_data) {
	  if (svn_source_data['svn_type'] == "exec") {
	    $("#svn_engine .data").html(App.lang("Executable"));
	    $("#svn_exec_path .data").html(App.lang(svn_source_data["svn_path"]));
	    $("#svn_config_path .data").html(App.lang(svn_source_data["svn_config_dir"]));
      $("#svn_trust_server_cert .data").html(svn_source_data["svn_trust_server_cert"] == "1" ? App.lang("Yes") : App.lang("No"));
	  } else if (svn_source_data['svn_type'] == "extension") {
	    $("#svn_engine .data").html(App.lang("PHP Extension"));
	    $("#svn_exec_path .data").html("-");
	    $("#svn_config_path .data").html("-");
      $("#svn_trust_server_cert .data").html("-");
	  }
	});
	
	App.Wireframe.Events.bind('mercurial_updated.content', function(event, mercurial_source_data) {
	  $("#mercurial_exec_path .data").html(App.lang(mercurial_source_data["mercurial_path"]));
	});

	$('#repositories').pagedObjectsList({
	  'load_more_url' : '{assemble route=admin_source}',
	  'items' : {$repositories|json nofilter},
	  'items_per_load' : {$repositories_per_page}, 
	  'total_items' : {$total_repositories}, 
	 
	  'list_items_are' : 'tr', 
	  'list_item_attributes' : { 'class' : 'repository' }, 
	  'columns' : {
	    'name' : App.lang('Repository Name'), 
	    'type' : App.lang('Repository Type'), 
	    'usage' : App.lang('Project Usage'), 
	    'options' : '' 
	  },
	  'empty_message' : App.lang('There are no repositories'),
	  'listen' : 'repository', 
	  'on_add_item' : function(item) {
	    var repository = $(this);
	    repository.append('<td class="name">' + 
	      '<td class="type"></td>' + 
	      '<td class="usage"></td>' + 
	      '<td class="options"></td>'
	    );
	    
	    repository.find('td.name').text(App.clean(item['name']));
	    repository.find('td.type').text(App.clean(item['type']));
	    {literal}
	      $('<a></a>').attr({'href' : item['urls']['usage'] , 'title' : item['name'] + ' repository usage'}).text(item['project_count']).appendTo(repository.find('td.usage')).flyout();
	    {/literal}
	    repository.find('td.options')
	      .append('<a href="' + item['urls']['edit'] + '" class="edit_repository" title="' + App.lang('Edit Repository') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
	      .append('<a href="' + item['urls']['delete'] + '" class="delete_repository" title="' + App.lang('Delete Repository') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
	    ;
	    repository.find('td.options a.edit_repository').flyoutForm({
	      'success_event' : 'repository_updated'
	    });
      
	    repository.find('td.options a.delete_repository').asyncLink({
        'confirmation' : App.lang('Are you sure that you want to permanently delete this repository? It will also be removed from all the projects.'),
        'success_event' : 'repository_deleted',
        'success_message' : App.lang('Repository has been deleted successfully')
	    });
	  }
	});
</script>