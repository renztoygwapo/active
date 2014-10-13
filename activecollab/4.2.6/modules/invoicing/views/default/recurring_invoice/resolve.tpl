{object object=$active_approval_request user=$logged_user}
	<div id="resolve_approval_request">
    <div class="resolve_form">
    	{form action=$active_approval_request->getResolveUrl() method=post}
    		{wrap field="option"}
    			{label for="resolution"}Resolve{/label}
    			<select name="approval_request[resolution]" id="resolution">
    				<option value={RecurringApprovalRequest::APPROVAL_REQUEST_APPROVED}>{lang}Approve{/lang}</option>
    				<option value={RecurringApprovalRequest::APPROVAL_REQUEST_DISAPPROVED}>{lang}Disapprove{/lang}</option>
    			</select>
    		{/wrap}
    		{wrap field="archive"}
      		{if $active_approval_request->getRecurringProfile()->isLastOccurrence()}
      			<p class="details">{lang}This is last occurrence of this recurring profile and it will be archived after this occurrence.{/lang}</p>
      		{else}
      			<input type="checkbox" name="approval_request[archive_it]" id="archive_it" value="1"/>
          		<p class="details">{lang}Archive this recurring profile after this occurrence.{/lang}</p>
          {/if}
    		{/wrap}
    	
      	{wrap_buttons}
        	{submit}Resolve Conflict{/submit}
      	{/wrap_buttons}
    	{/form}
  	</div>
  </div>
{/object}