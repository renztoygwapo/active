{wrap field=name}
  {text_field name='notebook[name]' value=$notebook_data.name label='Name' id=notebook_form_name required=true}
{/wrap}

{wrap_editor field=body}
  {editor_field name='notebook[body]' label='Description' id=notebook_form_body}{$notebook_data.body nofilter}{/editor_field}
{/wrap_editor}

{if $logged_user->canSeeMilestones($active_project)}
  {wrap field=milestone_id}
    {select_milestone name='notebook[milestone_id]' value=$notebook_data.milestone_id label='Milestone' id=notebook_form_milestone project=$active_project user=$logged_user}
  {/wrap}
{/if}

{if $logged_user->canSeePrivate()}
  {wrap field=visibility}
    {select_visibility name='notebook[visibility]' value=$notebook_data.visibility label='Visibility' object=$active_notebook}
  {/wrap}
{else}
  <input type="hidden" name="notebook[visibility]" value="1">
{/if}
  
{if $active_notebook->isNew()}  
  {wrap field=notify_users}
    {select_subscribers name="notify_users[]" exclude=$notebook_data.exclude_ids object=$active_notebook user=$logged_user label='Notify People'}
  {/wrap}
{/if}

<script type="text/javascript">
	$(document).ready(function() {
		App.Wireframe.SelectBox.init();
	});
</script>