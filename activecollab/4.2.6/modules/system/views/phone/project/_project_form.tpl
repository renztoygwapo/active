{wrap field=name}
  {text_field name="project[name]" value=$project_data.name id=project_form_name label='Name' required=true}
{/wrap}

{wrap field=overview}
  {editor_field name="project[overview]" id=project_form_summary label='Description'}{$project_data.overview nofilter}{/editor_field}
{/wrap}

{wrap field=leader_id}
  {select_user name="project[leader_id]" value=$project_data.leader_id id=project_form_leader exclude_ids=$project_data.exclude_ids label='Leader' user=$logged_user required=true}
{/wrap}

{wrap field=company_id}
  {select_company name="project[company_id]" value=$project_data.company_id id=project_form_company label='Client' user=$logged_user required=true}
{/wrap}

{if AngieApplication::isModuleLoaded('tracking')}
  {wrap field=budget}
    {money_field name="project[budget]" value=$project_data.budget label="Budget"}
  {/wrap}
  
  {wrap field=currency_id}
    {select_currency name="project[currency_id]" value=$project_data.currency_id label="Currency" optional=true}
  {/wrap}
{/if}

{wrap field=category_id}
  {select_project_category name="project[category_id]" value=$project_data.category_id id=project_form_category label='Category' user=$logged_user optional=true}
{/wrap}

{wrap field=label_id}
  {select_label name='project[label_id]' value=$project_data.label_id type=ProjectLabel user=$logged_user can_create_new=false label="Label" optional=true}
{/wrap}

<script type="text/javascript">
	$(document).ready(function() {
		App.Wireframe.SelectBox.init();
		App.Wireframe.DateBox.init();
	});
</script>