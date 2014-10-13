{title}People{/title}
{add_bread_crumb}All{/add_bread_crumb}

<div id="project_people">
  {foreach $project_users as $company_name => $users}
    <table class="common" cellspacing="0">
      <thead>
        <tr>
          <th colspan="{if $can_manage}4{else}3{/if}">{$company_name}</th>
        </tr>
      </thead>
      <tbody>
      {foreach $users as $user}
        <tr class="project_company_user" user_id="{$user->getId()}">
          <td class="avatar"><img src="{$user->avatar()->getUrl($smarty.const.IUserAvatarImplementation::SIZE_BIG)}"></td>
          <td class="name">
            <a href="{$user->getViewUrl()}" class="project_company_user_name">{$user->getDisplayName()}</a>
          {if $can_see_contact_details}
            <ul class="project_company_user_contact_details">
              <li>{lang}Email{/lang}: <a href="mailto:{$user->getEmail()}">{$user->getEmail()}</a></li>
            </ul>
          {/if}
          </td>
          <td class="role">
          {if $active_project->isLeader($user)}
            {lang}Full Access{/lang} <span>({lang}Project Leader{/lang})</span>
          {elseif $user->isAdministrator()}
            {lang}Full Access{/lang} <span>({lang}Administrator{/lang})</span>
          {elseif $user->isProjectManager()}
            {lang}Full Access{/lang} <span>({lang}Project Manager{/lang})</span>
          {else}
            {$user->projects()->getRoleName($active_project)}
          {/if}
          </td>
          {if $can_manage}
          <td class="options">
          {if $user->canChangeProjectPermissions($logged_user, $active_project)}
            <a href="{$active_project->getUserPermissionsUrl($user)}" title="{lang}Change Permissions{/lang}" class="change_permissions"><img src="{image_url name='icons/12x12/configure.png' module=$smarty.const.ENVIRONMENT_FRAMEWORK}"></a>
          {/if}
          {if $user->canReplaceOnProject($logged_user, $active_project)}
            <a href="{$active_project->getReplaceUserUrl($user)}" title="{lang}Replace User{/lang}" class="replace_user"><img src="{image_url name='icons/12x12/swap.png' module=$smarty.const.ENVIRONMENT_FRAMEWORK}"></a>
          {/if}
          {if $user->canRemoveFromProject($logged_user, $active_project)}
            <a href="{$active_project->getRemoveUserUrl($user)}" title="{lang}Remove User{/lang}" class="remove_user"><img src="{image_url name='icons/12x12/delete.png' module=$smarty.const.ENVIRONMENT_FRAMEWORK}"></a>
          {/if}
          </td>
          {/if}
        </tr>
      {/foreach}
      </tbody>
    </table>
  {/foreach}
</div>

<script type="text/javascript">
  $('#project_people').each(function() {
    var wrapper = $(this);

    wrapper.on('click', 'a.change_permissions', function(event) {
      App.Delegates.flyoutFormClick.apply(this, [event, {
        'success_event' : 'project_people_updated',
        'width' : 450,
        'success' : function() {
          App.Wireframe.Flash.success('User permissions have been updated');
        },
        'error' : function() {
          App.Wireframe.Flash.error('Failed to update user permissions. Please try again later');
        },
        'stop_propagation' : true
      }]);

      return false;
    });

    wrapper.on('click', 'a.replace_user', function(event) {
      App.Delegates.flyoutFormClick.apply(this, [event, {
        'success_event' : 'project_people_updated',
        'width' : '500',
        'success' : function(response) {
          App.Wireframe.Flash.success('Selected user has been replaced on this project');
        },
        'error' : function() {
          App.Wireframe.Flash.error('Failed to replace selected user on this project. Please try again later');
        },
        'stop_propagation' : true
      }]);

      return false;
    });

    wrapper.on('click', 'a.remove_user', function(event) {
      App.Delegates.flyoutFormClick.apply(this, [event, {
        'success_event' : 'project_people_updated',
        'width' : '500',
        'success' : function(response) {
          App.Wireframe.Flash.success('Selected user has been removed from this project');
        },
        'error' : function() {
          App.Wireframe.Flash.error('Failed to remove selected user from this project. Please try again later');
        },
        'stop_propagation' : true
      }]);

      return false;
    });

    // People added event handler
    App.Wireframe.Events.bind('project_people_created.content', function (event, response) {
      App.Wireframe.Content.reload();
      App.Wireframe.Flash.success('Selected people have been added to the project');
    });

    // People updated event handler
    App.Wireframe.Events.bind('project_people_updated.content', function (event, response) {
      App.Wireframe.Content.reload();
    });
  });
</script>