{title}Move to Notebook{/title}
{add_bread_crumb}Move to Notebook{/add_bread_crumb}

<div id="move_to_notebook">
  {form action=$active_notebook_page->getMoveUrl() method=post}
    <div class="fields_wrapper">
      <p>{lang type=$active_notebook_page->getVerboseType(true) name=$active_notebook_page->getName() notebook_name=$active_notebook->getName()}You are about to move :type "<b>:name</b>" from "<b>:notebook_name</b>" notebook. Please select a destination notebook{/lang}:</p>
      
      {wrap field=project_id}
        {select_notebook project=$active_project user=$logged_user name="notebook_id" label='Select Destination Notebook' skip=$active_notebook->getId()}
      {/wrap}
    </div>
    
    {wrap_buttons}
      {submit}Move to Notebook{/submit}
    {/wrap_buttons}
  {/form}
</div>