{title lang=false}{lang name=$active_project_request->getName()}':name' Project Request{/lang}{/title}
{add_bread_crumb}Details{/add_bread_crumb}
{use_widget name=properties_list module=$smarty.const.ENVIRONMENT_FRAMEWORK}

<div id="project_request_details">
  {if $request_submitted}
  <p class="page_message ok">
    {lang request_url=$active_project_request->getPublicUrl()}Thank you for submitting project request.<br/><span class="label">Note:</span> Make sure to bookmark this URL so you can track your request's progress:<br/><a href=":request_url">:request_url</a>{/lang}<br/><br/>
  </p>
  {/if}

  {if $project_request_expired}
  <p class="page_message warning">{lang}<span class="label">Important</span>: This project request is marked as closed, so access to this page is no longer available to clients{/lang}</p>
  {/if}
  <dl class="properties_list">
    <dt>{lang}Requested By{/lang}</dt>
    <dd>{user_link user=$active_project_request->getCreatedBy()} ({$active_project_request->getCreatedByCompanyName()})</dd>
    
    <dt>{lang}Description{/lang}</dt>
	  <dd>{$active_project_request->getBody()|rich_text nofilter}</dd>
    
	{foreach $active_project_request->getCustomFields() as $custom_field_key => $custom_field}
	  <dt>{$custom_field.label}</dt>
	  <dd>
	  {if $custom_field.value}
	    {$custom_field.value}
	  {else}
	    --
	  {/if}
	  </dd>
	{/foreach}
  </dl>
  
  {frontend_object_comments object=$active_project_request user=$logged_user errors=$errors post_comment_url=$active_project_request->getPublicUrl() comment_data=$comment_data}
</div>