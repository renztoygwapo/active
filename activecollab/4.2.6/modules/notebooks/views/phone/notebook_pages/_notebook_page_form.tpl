{wrap field=name}
  {text_field name='notebook_page[name]' value=$notebook_page_data.name label='Name' id=notebook_page_name_form required=true}
{/wrap}

{wrap_editor field=body}
  {editor_field name='notebook_page[body]' label='Body' id=notebook_page_body_form}{$notebook_page_data.body nofilter}{/editor_field}
{/wrap_editor}

{if !$active_notebook_page->isNew()}  
  {wrap field=is_minor_revision}
  	<label class="main_label ui-input-text">{lang}Version{/lang}:</label>
	    <fieldset data-role="controlgroup" data-theme="j">
    		<input type="radio" name="notebook_page[is_minor_revision]" value="0" id="createNewPageVersion" class="auto" data-theme='i' {if !$notebook_page_data.is_minor_revision}checked="checked"{/if} /> <label for="createNewPageVersion" class="auto">{lang}Create a new page version{/lang}</label>
    		<input type="radio" name="notebook_page[is_minor_revision]" value="1" id="notebookPageIsMinorRevision" class="auto" data-theme='i' {if $notebook_page_data.is_minor_revision}checked="checked"{/if} /> <label for="notebookPageIsMinorRevision" class="auto">{lang}This is just a minor change{/lang}</label>
	    </fieldset>
  {/wrap}
{/if}

{wrap field=parent_id}
  {select_notebook_page name='notebook_page[parent_id]' value=$notebook_page_data.parent_id project=$active_project notebook=$active_notebook skip=$active_notebook_page user=$logged_user label='File Under'}
{/wrap}

{if $active_notebook_page->isNew()}  
  {wrap field=notify_users}
    {select_subscribers name="notify_users[]" exclude=$notebook_page_data.exclude_ids object=$active_notebook_page user=$logged_user label='Notify People'}
  {/wrap}
{/if}

<input type="hidden" name="notebook_page[notebook_id]" value="{$notebook_id}">

<script type="text/javascript">
	$(document).ready(function() {
		App.Wireframe.SelectBox.init();
	});
</script>