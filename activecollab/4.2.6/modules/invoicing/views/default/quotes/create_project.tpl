{title}Convert Quote to Project{/title}
{add_bread_crumb}Convert to Project{/add_bread_crumb}

<div id="quote_won">
  {form action=$active_quote->getCreateProjectUrl() method=post}
    <p><input type="radio" name="quote[create_project]" value="1" {if !$quote_data.create_project}checked="checked"{/if} class="inline input_radio" id="wonFormCreateProjectYes" /> {label for="wonFormCreateProjectYes" class=inline}Convert to Project{/label}</p>
    <div id="select_quote_recipients" style="display: none">
    	<input type="checkbox" name="quote[create_milestones]" value="1" id="createMilestones" class="inline" {if $quote_data.create_milestones}checked="checked"{/if} /> <label for="createMilestones" class="inline">{lang}Create Milestones based on quote items{/lang}</label>
    </div>
    
    {wrap_buttons}
      {submit}Convert Quote to Project{/submit}
    {/wrap_buttons}
  {/form}
  
  {empty_slate name=won module=invoicing}
</div>