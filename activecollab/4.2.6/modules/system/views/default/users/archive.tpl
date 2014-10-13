{title}Users{/title}
{add_bread_crumb}Archived Users{/add_bread_crumb}

<div id="users_archive">
	{if is_foreachable($archived_users)}
	  <table class="users_archive common" cellspacing="0">
	    <tr>
	      <th class="avatar"></th>
	      <th class="name">{lang}User{/lang}</th>
        <th class="archived_on">{lang}Archived On{/lang}</th>
	      <th class="options">{lang}Action{/lang}</th>
	    </tr>
	  {foreach from=$archived_users item=archived_user}
	    <tr class="{cycle values='odd,even'}">
	      <td class="avatar"><img src="{$archived_user->avatar()->getUrl(IUserAvatarImplementation::SIZE_SMALL)}" alt="" /></td>
	      <td class="name">{user_link user=$archived_user}</td>
        <td class="archived_on">{if $archived_user->getUpdatedOn()}{$archived_user->getUpdatedOn()|datetime}{/if}</td>
	      <td class="options">
	      {if $archived_user->state()->canTrash($logged_user)}
	        {link href=$archived_user->state()->getTrashUrl() title='Move to Trash' class=move_to_trash}<img src="{image_url name="icons/12x12/move-to-trash.png" module=$smarty.const.SYSTEM_MODULE}" alt="" />{/link}
	      {/if}
	      
	      {if $archived_user->state()->canUnarchive($logged_user)}
	        {link href=$archived_user->state()->getUnarchiveUrl() title='Restore from Archive' class=restore_from_archive}<img src="{image_url name="icons/12x12/unarchive.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="" />{/link}
	      {/if}
	      </td>
	    </tr>
	  {/foreach}
	  </table>
	{/if}
	
	<p class="empty_page" {if is_foreachable($archived_users)}style="display: none"{/if}><span class="inner">{lang}This company has no archived users{/lang}</span></p>
</div>

<script type="text/javascript">
  $('#users_archive').each(function() {
    var wrapper = $(this);

    var users_archive_table = wrapper.find('table.users_archive');
    var empty_page = wrapper.find('p.empty_page');
    
    users_archive_table.each(function() {
      var table = $(this);
      
      var reindex_even_odd_rows = function(table) {
        var counter = 1;
        table.find('tr').each(function() {
          $(this).removeClass('odd').removeClass('even').addClass(counter % 2 ? 'odd' : 'even');
          counter++;
        });
      }
      
      var update_tab_counter = function() {
        var tabs = $('div.inline_tabs_links').find('ul li a');
        tabs.each(function() {
        	var tab_counter = $(this).find('span');
        	if($(this).hasClass('selected')) {
	          tab_counter.html(parseInt(tab_counter.html(), 10) - 1);
          } // if
        });
      }
      
      table.find('td.options a.move_to_trash').click(function() {
        var link = $(this);
        
        // Block additional clicks
        if(link[0].block_clicks) {
          return false;
        } // if
        
        if(confirm(App.lang('Are you sure that you want to move this user to trash?'))) {
        	link[0].block_clicks = true;
        	
          var row = link.parent().parent();
          var img = link.find('img');
          var old_src = img.attr('src');
          
          img.attr('src', App.Wireframe.Utils.indicatorUrl());
          
          $.ajax({
            url : link.attr('href'),
            type : 'POST',
            data : {
              'submitted' : 'submitted'
            },
            success : function(response) {
              row.remove();
              update_tab_counter();
              if(users_archive_table.find('tr').length != 1) {
                reindex_even_odd_rows(table);
              } else {
                table.hide();
                empty_page.show();
              } // if
            },
            error   : function() {
              img.attr('src', old_src);
            }
          });
        } // if
        
        return false;
      });
      
      table.find('td.options a.restore_from_archive').click(function() {
        var link = $(this);
        
        // Block additional clicks
        if(link[0].block_clicks) {
          return false;
        } // if
        
        if(confirm(App.lang('Are you sure that you want to restore this user from archive?'))) {
        	link[0].block_clicks = true;
        	
          var row = link.parent().parent();
          var img = link.find('img');
          var old_src = img.attr('src');
          
          img.attr('src', App.Wireframe.Utils.indicatorUrl());
          
          $.ajax({
            url : link.attr('href'),
            type : 'POST',
            data : {
              'submitted' : 'submitted'
            },
            success : function(response) {
              row.remove();
              update_tab_counter();
              App.Wireframe.Events.trigger('user_updated.content', response);
              if(users_archive_table.find('tr').length != 1) {
                reindex_even_odd_rows(table);
              } else {
                table.hide();
                empty_page.show();
              } // if
            },
            error   : function() {
              img.attr('src', old_src);
            }
          });
        } // if
        
        return false;
      });

      // handle behavior when archived user is restored from trash
      App.Wireframe.Events.bind('user_updated.single', function (event, user) {
        if (user.is_archived) {
          var tabs = $('div.inline_tabs_links').find('ul li a');
          tabs.each(function() {
            var tab_counter = $(this).find('span');
            if($(this).hasClass('selected')) {
              tab_counter.html(parseInt(tab_counter.html(), 10) + 1);
              tab_counter.parent().click();
            } // if
          });
        } // if
      });
    });
  });

  // refresh page when company is added
  var inline_tabs = $('#users_archive').parents('.inline_tabs:first');
  if (inline_tabs.length) {
    var tabs_id = inline_tabs.attr('id');
    App.Wireframe.Events.bind('company_updated', function (event, company) {
      if (company.id == {$active_company->getId()}) {
        App.widgets.InlineTabs.refresh(tabs_id);
      } // if
    });
  } // if

</script>