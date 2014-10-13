{title}Edit Repsite Domain{/title}
{add_bread_crumb}Edit Repsite Domain{/add_bread_crumb}


<div id="add_new_page">
{form action=Router::assemble('repsite_admin_edit_repsite_domain')}
	<div class="content_stack_wrapper">
		<div class="content_stack_element">
	        <div class="content_stack_element_body">
	          		{wrap field=repsite_domain}
            			{text_field name="config_opt[rep_site_domain]" value=$config_opt.rep_site_domain label='Default Rep Site Domain'}
		            	<p class="aid">{lang}Repsite Domain Name eg: abuckagallon.com{/lang}</p>
		            {/wrap}
        	</div>
	    </div>


	</div>
	{wrap_buttons}
		{submit}Save{/submit}
	{/wrap_buttons}
{/form}    
</div>

<script type="text/javascript">
$(document).ready(function(){
	
});
</script>
