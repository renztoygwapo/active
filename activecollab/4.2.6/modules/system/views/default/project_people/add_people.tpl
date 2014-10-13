{title}Add Users{/title}
{add_bread_crumb}Add{/add_bread_crumb}

<div id="add_people">
{if $is_empty_select}
  <p class="empty_page">{lang}There are no users to be added{/lang}</p>
{else}
  {form action=$active_project->getAddPeopleUrl() method=post}
    {wrap_fields style="min-height: 300px;"}
      {wrap field=users visible_overflow=true}
        {if $logged_user->isOwner() || $logged_user->isAdministrator() || $logged_user->isProjectManager() || $logged_user->isPeopleManager()}
          {select_users name=users exclude=$exclude_users label='Select Users' user=$logged_user mode=input width=600 required=true}
        {else}
          {select_users name=users object=$logged_user->getCompany() exclude=$exclude_users label='Select Users' user=$logged_user mode=input width=600 required=true}
        {/if}
      {/wrap}

      {wrap field=user_permissions}
        {select_user_project_permissions name=project_permissions role_id=$default_project_role_id label='Permissions' required=true}
      {/wrap}
    {/wrap_fields}

    {wrap_buttons}
      {submit}Add People{/submit}
    {/wrap_buttons}
  {/form}
{/if}
</div>

<script type="text/javascript">
    // this is to make chosen control required
    var form = $('#add_people');
    var control = form.find('.chzn-done');
    var wrapper = control.parents('div.select_users_input:first');
    var chosen_container = form.find('.chzn-container:first');

    control.attr('required', 'required');
    control.show().css({
      'position'        : 'absolute',
      'height'          : chosen_container.height(),
      'padding-top'     : '0px',
      'padding-bottom'  : '0px'
    });
</script>