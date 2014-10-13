{title}View Filter{/title}
{add_bread_crumb}View Filter{/add_bread_crumb}

<div id="filter" class="content_stack_wrapper">
  <div class="content_stack_element" id="details">
    <div class="content_stack_element_info">
      <h3>{lang}Filter Details{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      {if $active_filter->getName()}
        <dt>{lang}Name{/lang}</dt>
     	  <dd>{$active_filter->getName()}</dd>
      {/if}
      {if $active_filter->getDescription()}
      	<dt>{lang}Description{/lang}</dt>
     	  <dd>{$active_filter->getDescription()}</dd>
      {/if}
      
      {if $active_filter->getMailboxId()}
        <dt>{lang}Mailbox{/lang}</dt>
        <dd>{implode values=IncomingMailboxes::listNamesByIds($active_filter->getMailboxId()) separator=' , '}</dd>
      {/if}
      
      {if $active_filter->getBody()}
     	  <dt>{lang}Body{/lang}</dt>
      	<dd>{$active_filter->getBodyType()} : {$active_filter->getBodyText()}</dd>
      {/if}
      {if $active_filter->getSubject()}
     	  <dt>{lang}Subject{/lang}</dt>
      	<dd>{$active_filter->getSubjectType()} : {$active_filter->getSubjectText()}</dd>
      {/if}
      {if $active_filter->getPriority()}
     	  <dt>{lang}Priority{/lang}</dt>
      	<dd>{$active_filter->getPriority()}</dd>
      {/if}
      
      
      {if $active_filter->getAttachments()}
     	  <dt>{lang}Attachments{/lang}</dt>
      	<dd>{$active_filter->getAttachments()}</dd>
      {/if}
      {if $active_filter->getSender()}
        <dt>{lang}Sender{/lang}</dt>
        {if $active_filter->getSenderText()}
          <dd>{$active_filter->getSenderType()} : {$active_filter->getSenderText()}</dd>
      	{else}
      		<dd>{$active_filter->getSenderType()}</dd>
      	{/if}
      {/if}
      
      	<dt>{lang}Action name:{/lang}</dt>
      	<dd>{$active_filter->getActionObject()->getName()}</dd>
      	
      	{if $active_filter->getActionParameter('project_id')}
        	<dt>{lang}In project:{/lang}</dt>
        	<dd>{Projects::findById($active_filter->getActionParameter('project_id'))->getName()}</dd>
        {/if}
     
      {if $active_filter->getActionObject()->getDescription()}
        <dt>{lang}Description{/lang}</dt>
      	<dd>{$active_filter->getActionObject()->getDescription()}</dd>
      {/if}
      
    </div>
  </div>
 </div>