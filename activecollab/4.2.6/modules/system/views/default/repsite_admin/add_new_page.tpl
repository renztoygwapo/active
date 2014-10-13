{title}Add New Page{/title}
{add_bread_crumb}New Page{/add_bread_crumb}
{use_widget name="repsite_admin_page" module="system"}

<div id="add_new_page">
{form action=Router::assemble('repsite_admin_add_new_page')}
	<div class="content_stack_wrapper">
		<div class="content_stack_element">
	        <div class="content_stack_element_info">
	          	<h3>{lang}Page Name{/lang}</h3>
	        </div>
	        <div class="content_stack_element_body">
	          	{wrap field=name}
	            	{text_field rows=700 name="data[name]" value='' label="New Page name" required=true}

	          	{/wrap}
	          	<p class="aid">{lang}Will be the page unique url <i>(eg:domain.com/index.php?page=new-page-url){/lang}</i></p>
        	</div>
	    </div>

	    <div class="content_stack_element">
	        <div class="content_stack_element_info">
	          	<h3>{lang}Page HTML:{/lang}</h3>
	        </div>
	        <div class="content_stack_element_body">
	          	{wrap_editor field=overview}
			      {label}Description{/label}
			      {editor_field class="new_page_html" name="data[page_html]" images_enabled=false id="page_html_editor"}{$data.page_html nofilter}{/editor_field}
			    {/wrap_editor}

        	</div>
	    </div>

	</div>
	{wrap_buttons}
		{submit}Add Page{/submit}
	{/wrap_buttons}
{/form}    
</div>

<script type="text/javascript">
$(document).ready(function(){
	
});
</script>