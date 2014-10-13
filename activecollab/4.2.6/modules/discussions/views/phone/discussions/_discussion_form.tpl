{wrap field=name}
  {text_field name="discussion[name]" value=$discussion_data.name id=discussionSummary label='Summary' required=true}
{/wrap}

{wrap field=body}
  {editor_field name="discussion[body]" id=discussionBody label='Description' required=true}{$discussion_data.body nofilter}{/editor_field}
{/wrap}

{wrap field=category_id}
  {select_discussion_category name="discussion[category_id]" value=$discussion_data.category_id parent=$active_project user=$logged_user id=discussionCategory label='Category'}
{/wrap}
  
{if $logged_user->canSeeMilestones($active_project)}
  {wrap field=milestone_id}
    {select_milestone name="discussion[milestone_id]" value=$discussion_data.milestone_id project=$active_project user=$logged_user id=discussionMilestone label='Milestone'}
  {/wrap}
{/if}

{if $logged_user->canSeePrivate()}
  {wrap field=visibility}
    {select_visibility name="discussion[visibility]" value=$discussion_data.visibility id=discussionVisibility label='Visibility' object=$active_discussion}
  {/wrap}
{else}
  <input type="hidden" name="discussion[visibility]" value="1" />
{/if}
  
{if $active_discussion->isNew()}
  {wrap field=notify_users}
    {select_subscribers name="notify_users[]" exclude=$discussion_data.exclude_ids object=$active_discussion user=$logged_user id=discussionNotifyPeople label='Notify People'}
  {/wrap}
{/if}

<script type="text/javascript">
	$(document).ready(function() {
		App.Wireframe.SelectBox.init();
	});
</script>