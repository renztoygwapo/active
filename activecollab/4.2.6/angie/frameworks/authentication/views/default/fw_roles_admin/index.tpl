{title}All System Roles{/title}
{add_bread_crumb}All System Roles{/add_bread_crumb}

<div id="user_roles">
  <table class="common" cellspacing="0" style="width: 300px">
    <tr>
      <th class="icon"></th>
      <th class="name">{lang}Name{/lang}</th>
      <th class="number_of_users right">{lang}Number of Users{/lang}</th>
    </tr>
  {foreach $roles as $role_class => $role_details}
    <tr>
      <td class="icon"><img src="{$role_details.icon}"></td>
      <td class="name">{$role_details.name}</td>
      <td class="number_of_users right">
        {if $role_details.users_count == 0}
          {lang num=0}:num Users{/lang}
        {elseif $role_details.users_count == 1}
          <a href="{$role_details.url}" title="{$role_details.name}">{lang}One User{/lang}</a>
        {else}
          <a href="{$role_details.url}" title="{$role_details.name}">{lang num=$role_details.users_count}:num Users{/lang}</a>
        {/if}
      </td>
    </tr>
  {/foreach}
  </table>
</div>

<script type="text/javascript">
  $('#user_roles td.number_of_users a').flyout({
    'width' : 500,
  });
</script>