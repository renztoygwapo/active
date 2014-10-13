{title}Users{/title}
{add_bread_crumb}All Users{/add_bread_crumb}
{use_widget name="objects_list" module="environment"}

<div id="users">
  <div class="empty_content">
    <div class="objects_list_title">{lang}Users{/lang}</div>
    <div class="objects_list_icon"><img src="{image_url name='icons/48x48/users.png' module=$smarty.const.AUTHENTICATION_FRAMEWORK}" alt=""/></div>
    <div class="objects_list_details_actions">
      {if Users::canAdd($this->logged_user)}
        <ul>
          <li><a href="{assemble route='users_add'}" id="new_user" class="single">{lang}New User{/lang}</a></li>
        </ul>
      {/if}
    </div>
      
    <div class="object_lists_details_tips">
      <h3>{lang}Tips{/lang}:</h3>
      <ul>
        <li>{lang}To select a user and load its details, please click on it in the list on the left{/lang}</li>
        <li>{lang}It is possible to select multiple users at the same time. Just hold Ctrl key on your keyboard and click on all the users that you want to select{/lang}</li>
      </ul>
    </div>
  </div>
  
  <div class="multi_content">TODO</div>
</div>

<script type="text/javascript">
  $('#users').objectsList({
    'id' : 'fw_users',
    'items' : {$users|json nofilter},
    'required_fields' : ['id', 'display_name', 'permalink'], 
    'objects_type' : 'users',
    'multi_title' : App.lang(':num Users Selected'),
    'render_item' : function (item) {
      return '<td class="name">' + App.clean(item['display_name']) + '</td>';
    },
    'grouping' : [{
      'label' : App.lang("Don't group"), 
      'property' : '', 
      'icon' : App.Wireframe.Utils.imageUrl('objects-list/dont-group.png', 'environment'), 
      'default' : true 
    }, {
      'label' : App.lang('By Group'), 
      'property' : 'group_id', 
      'map' : {}, 
      'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-category.png', 'categories')
    }], 
    'filtering' : [{
      'label' : App.lang('Status'), 'property' : 'state', 'values'  : [{ 
		    'label' : App.lang('Active'), 
		    'value' : '3', 
		    'icon' : App.Wireframe.Utils.imageUrl('objects-list/active.png', 'complete'), 
		    'default' : true, 
		    'breadcrumbs' : App.lang('Active')
		  }, { 
		    'label' : App.lang('Archived'), 
        'value' : '1', 
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/completed.png', 'complete'), 
        'breadcrumbs' : App.lang('Completed') 
      }]
  	}]
  });
</script>