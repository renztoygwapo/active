<script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div class="big_form_wrapper two_form_sidebars">
  <div class="main_form_column">
    {wrap field=name}
      {text_field name='notebook_page[name]' value=$notebook_page_data.name id=notebookPageName class='title required validate_minlength 3' label="Title" maxlength="150" required=true}
    {/wrap}
    
    {wrap_editor field=body}
      {label}Description{/label}
      {editor_field name='notebook_page[body]' id=notebookPageBody class='validate_callback tiny_value_present' inline_attachments=$notebook_page_data.inline_attachments auto_expand=false object=$active_notebook}{$notebook_page_data.body nofilter}{/editor_field}
    {/wrap_editor}
  </div>
  
  <div class="form_sidebar form_first_sidebar">
	{if !$active_notebook_page->isNew()}  
    {wrap field=is_minor_revision}
      {checkbox name="notebook_page[is_minor_revision]" checked=$notebook_page_data.is_minor_revision label="This is just a minor revision"}
      <p class="aid">{lang}System does not create a new page version or sends out email notifications on minor revision{/lang}</p>
    {/wrap}
  {/if}
    
    {wrap field=parent_id}
      {select_notebook_page name='notebook_page[parent_id]' value=$notebook_page_data.parent_id project=$active_project notebook=$active_notebook skip=$active_notebook_page user=$logged_user label='File Under'}
    {/wrap}
    
    {wrap field=attachments}
      {select_attachments name="notebook_page[attachments]" object=$active_notebook_page user=$logged_user label='Attachments'}
    {/wrap}
  </div>
  
  <div class="form_sidebar form_second_sidebar">
      {wrap field=notify_users}
        {select_subscribers name=notify_users value=$active_notebook_page->subscriptions()->getIds() exclude=$notebook_page_data.exclude_ids object=$active_notebook_page user=$logged_user label='Notify People'}
      {/wrap}
    
  </div>
  
  <input type="hidden" name="notebook_page[notebook_id]" value="{$notebook_id}">
</div>