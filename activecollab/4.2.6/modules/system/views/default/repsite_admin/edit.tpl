{title}Edit Respsite Page{/title}
{add_bread_crumb}Edit Repsite Page{/add_bread_crumb}
{use_widget name="repsite_admin_page" module="system"}

<div id="edit_page">
{form action='' onsubmit="return getSubmitDivEditable()"}
	<div class="content_stack_wrapper">
		<div class="content_stack_element">
	        <div class="content_stack_element_info">
	          	<h3>{lang}Page Name{/lang}</h3>
	        </div>
	        <div class="content_stack_element_body">
	          	{wrap field=name}
	            	{text_field rows=700 name="data[name]" value=$page_data.name label="Page name" required=true}

	          	{/wrap}
	          	<p class="aid">{lang}Will be the page unique url <i>(eg:domain.com/index.php?page={$page_data.page_url}){/lang}</i></p>	
        	</div>	
	    </div>

	    <div class="content_stack_element">
	    	<div class="content_stack_element_info">
	        </div>
	        <div class="content_stack_element_body">

	        	{wrap_editor field=overview}
			      {label}Description{/label}
			      {editor_field class="new_page_html" name="data[page_html]" images_enabled=false id="page_html_textarea"}{$page_data.page_html nofilter}{/editor_field}
			    {/wrap_editor}
	        	
	          	
        	</div>
	    </div>

	</div>
	{wrap_buttons}
		{submit}Save Changes{/submit}
		<a href="{Router::assemble('repsite_admin')}" class="comment_cancel">{lang}Cancel{/lang}</a>
	{/wrap_buttons}
{/form}  
</div>