{title}Update Announcement{/title}
{add_bread_crumb}Update Announcement{/add_bread_crumb}

<div id="update_announcement" class="announcements_form">
  {form action=$active_announcement->getEditUrl()}
    {wrap_fields}
      {include file=get_view_path('_announcement_form', 'fw_announcements_admin', $smarty.const.ANNOUNCEMENTS_FRAMEWORK)}
    {/wrap_fields}

    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>