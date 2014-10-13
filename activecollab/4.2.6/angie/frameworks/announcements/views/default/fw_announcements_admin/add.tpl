{title}New Announcement{/title}
{add_bread_crumb}New Announcement{/add_bread_crumb}

<div id="add_announcement" class="announcements_form">
  {form action=Router::assemble('admin_announcements_add')}
    {wrap_fields}
      {include file=get_view_path('_announcement_form', 'fw_announcements_admin', $smarty.const.ANNOUNCEMENTS_FRAMEWORK)}
    {/wrap_fields}

    {wrap_buttons}
      {submit}Add Announcement{/submit}
    {/wrap_buttons}
  {/form}
</div>