{wrap field=name}
  {text_field name="text_document[name]" value=$text_document_data.name label='Summary' id=text_document_summary required=true}
{/wrap}

{wrap_editor field=body}
  {editor_field name="text_document[body]" label='Body' id=text_document_body}{$text_document_data.body nofilter}{/editor_field}
{/wrap_editor}

{if $active_asset->isLoaded()}
  {wrap field=create_new_version}
  	<label class="main_label ui-input-text">{lang}Version{/lang}:</label>
    <fieldset data-role="controlgroup" data-theme="j">
     	<input type="radio" name="text_document[create_new_version]" value="1" id="createNewTextDocumentVersion" class="auto" data-theme="i" {if $text_document_data.create_new_version}checked="checked"{/if} /> <label for="createNewTextDocumentVersion" class="auto">{lang}Create a new document version{/lang}</label>
			<input type="radio" name="text_document[create_new_version]" value="0" id="dontCreateNewTextDocumentVersion" class="auto" data-theme="i" {if !$text_document_data.create_new_version}checked="checked"{/if} /> <label for="dontCreateNewTextDocumentVersion" class="auto">{lang}This is minor change{/lang}</label>
    </fieldset>
  {/wrap}
{/if}

{wrap field=parent_id}
  {select_asset_category name="text_document[category_id]" value=$text_document_data.category_id id=text_document_category parent=$active_project user=$logged_user label='Category'}
{/wrap}

{if $logged_user->canSeeMilestones($active_project)}
  {wrap field=milestone_id}
    {select_milestone name="text_document[milestone_id]" value=$text_document_data.milestone_id project=$active_project id=text_document_milestone user=$logged_user label='Milestone'}
  {/wrap}
{/if}

{if $logged_user->canSeePrivate()}
  {wrap field=visibility}
    {select_visibility name="text_document[visibility]" value=$text_document_data.visibility id=text_document_visibility label='Visibility' object=$active_asset}
  {/wrap}
{else}
  <input type="hidden" name="text_document[visibility]" value="1" />
{/if}

{if $active_asset->isNew()}
	{wrap field=notify_users}
	  {select_subscribers name="notify_users[]" exclude=$text_document_data.exclude_ids object=$active_asset user=$logged_user id=text_document_notify_people label='Notify People'}
	{/wrap}
{/if}

<script type="text/javascript">
	$(document).ready(function() {
		App.Wireframe.SelectBox.init();
	});
</script>