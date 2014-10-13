<script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div class="big_form_wrapper two_form_sidebars">
  <div class="main_form_column">
    {wrap field=name}
      {text_field name="text_document[name]" value=$text_document_data.name id=textDocumentSummary class='title required validate_minlength 3' label="Title" maxlength="150" required=true}
    {/wrap}
    
    {wrap_editor field=body}
      {label}Content{/label}
      {editor_field name="text_document[body]" id=textDocumentBody inline_attachments=$text_document_data.inline_attachments object=$active_asset}{$text_document_data.body nofilter}{/editor_field}
    {/wrap_editor}
  </div>
  
  <div class="form_sidebar form_first_sidebar">
    {if $active_asset->isLoaded()}
      {wrap field=create_new_version}
        <input type="radio" name="text_document[create_new_version]" value="1" id="createNewTextDocumentVersion" class="inline" {if $text_document_data.create_new_version}checked="checked"{/if} /> {label for=createNewTextDocumentVersion class=inline main_label=false after_text=''}Create a new document version{/label}<br />
        <input type="radio" name="text_document[create_new_version]" value="0" id="dontCreateNewTextDocumentVersion" class="inline" {if !$text_document_data.create_new_version}checked="checked"{/if} /> {label for=dontCreateNewTextDocumentVersion class=inline main_label=false after_text=''}This is minor change{/label}
      {/wrap}
    {/if}
  
    {wrap field=parent_id}
      {label for=textDocumentCategory}Category{/label}
      {select_asset_category name="text_document[category_id]" value=$text_document_data.category_id id=textDocumentCategory parent=$active_project user=$logged_user success_event="category_created"}
    {/wrap}
    
    {if $logged_user->canSeeMilestones($active_project)}
      {wrap field=milestone_id}
        {label for=textDocumentMilestone}Milestone{/label}
        {select_milestone name="text_document[milestone_id]" value=$text_document_data.milestone_id project=$active_project id=textDocumentMilestone user=$logged_user}
      {/wrap}
    {/if}
    
    {if $logged_user->canSeePrivate()}
      {wrap field=visibility}
        {label for=textDocumentVisibility}Visibility{/label}
        {select_visibility name="text_document[visibility]" value=$text_document_data.visibility short_description=true object=$active_asset}
      {/wrap}
    {else}
      <input type="hidden" name="text_document[visibility]" value="1" />
    {/if}
  </div>
  
  <div class="form_sidebar form_second_sidebar">
  {if $active_asset->isNew()}
    {wrap field=notify_users}
      {select_subscribers name="notify_users" exclude=$text_document_data.exclude_ids object=$active_asset user=$logged_user label='Notify People'}
    {/wrap}
  {/if}
  </div>
</div>