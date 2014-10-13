{title}Invite People{/title}
{add_bread_crumb}Invite People{/add_bread_crumb}
{use_widget name="invite_people" module="system"}

<div id="people_invite">
  {form action=Router::assemble('people_invite')}
    {include file=get_view_path('_invite_form', 'people', $smarty.const.SYSTEM_MODULE)}

  {if AngieApplication::behaviour()->isTrackingEnabled()}
    <input type="hidden" name="_intent_id" value="{AngieApplication::behaviour()->recordIntent('people_invited')}">
  {/if}

    {wrap_buttons}
      {submit}Invite{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $("#people_invite").invitePeople();
</script>