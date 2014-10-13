<div id="print_container">
{object object=$active_company user=$logged_user}
	<div class="wireframe_content_wrapper">{project_list projects=$company_projects}</div>
	<div class="wireframe_content_wrapper">{user_list users=$active_company->getUsers()}</div>
	<div class="wireframe_content_wrapper">{user_list users=$active_company->getArchivedUsers() label="Archived Users" archived=true}</div>
	<div class="wireframe_content_wrapper">
  {if is_foreachable($company_invoices)}
    <h2>{lang}Invoices{/lang}</h2>

    <table class="common" cellspacing="0">
      <thead>
        <tr>
          <th class="invoice">{lang}Invoice{/lang} #</th>
          <th class="project">{lang}Project{/lang}</th>
          <th class="status center">{lang}Status{/lang}</th>
          <th class="due right">{lang}Payment Due On{/lang}</th>
        </tr>
      </thead>
      <tbody>
      {foreach $company_invoices as $object}
        <tr>
          <td class="invoice_name">{$object->getName()}</td>
          <td class="project">
            {if $object->getProject()}
              {$object->getProject()->getName()}
            {else}
              --
            {/if}
          </td>
          <td class="status center" style="width: 100px">{$object->getVerboseStatus()}</td>
          <td class="due right" style="width: 150px">
            {if $object->getDueOn()}
              {$object->getDueOn()|date:0}
            {else}
              --
            {/if}
          </td>
        </tr>
      {/foreach}
      </tbody>
    </table>
  {/if}
  </div>
	<div class="wireframe_content_wrapper">
  {if is_foreachable($company_quotes)}
    <h2>{lang}Quotes{/lang}</h2>

    <table class="common" cellspacing="0">
      <thead>
      <tr>
        <th class="quotes">{lang}Quote{/lang}</th>
        <th class="status center">{lang}Status{/lang}</th>
        <th class="created_on right">{lang}Created On{/lang}</th>
      </tr>
      </thead>
      <tbody>
      {foreach $company_quotes as $object}
        <tr>
          <td class="quote_name">{$object->getName()}</td>
          <td class="status center" style="width: 100px">{$object->getVerboseStatus()}</td>
          <td class="created right" style="width: 150px">
          {if $object->getCreatedOn()}
            {$object->getCreatedOn()|date:0}
          {else}
            --
          {/if}
          </td>
        </tr>
      {/foreach}
      </tbody>
    </table>
  {/if}
  </div>
{/object}
</div>