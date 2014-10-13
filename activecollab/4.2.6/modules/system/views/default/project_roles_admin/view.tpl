{title lang=false}{$active_role->getName()}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

<div id="role">
{if is_foreachable($project_users)}
  <table class="common">
    <thead>
      <tr>
        <th>{lang role_name=$active_role->getName()}Users with <span class="active_role_name">:role_name</span> project role{/lang}</th>
        <th colspan="2">{lang}In Project{/lang}</th>
      </tr>
    </thead>
    <tbody>
    {foreach $project_users as $project_user}
      {assign var="user" value=Users::findById($project_user.user_id)}
      <tr>
        <td class="name company">{user_link user=$user} {lang}of{/lang} {$user->getGroupName()}</td>
        <td class="project name">{project_link project=Projects::findById($project_user.project_id)}</td>
        <td class="checkbox">
          {if $user->getState() == $smarty.const.STATE_ARCHIVED}
            <img src="{image_url name='icons/12x12/unarchive.png' module=$smarty.const.ENVIRONMENT_FRAMEWORK}" title="{lang}User is archived{/lang}" alt="{lang}User is archived{/lang}" />
          {/if}
          {if $user->getState() == $smarty.const.STATE_TRASHED}
            <img src="{image_url name='icons/12x12/trashed.png' module=$smarty.const.ENVIRONMENT_FRAMEWORK}" title="{lang}User is in Trash{/lang}" alt="{lang}User is archived{/lang}" />
          {/if}
        </td>
      </tr>
    {/foreach}
    </tbody>
  </table>
{else}
  <p class="empty_page"><span class="inner">{lang role_name=$active_role->getName()}There are no users with <span class="active_role_name">:role_name</span> role{/lang}</span></p>
{/if}
</div>

<script type="text/javascript">
  App.Wireframe.Events.bind('role_updated.{$request->getEventScope()}', function (event, role) {
    App.Wireframe.PageTitle.set(role['name']);
    $('#role span.active_role_name').text(role['name']);
  });
</script>