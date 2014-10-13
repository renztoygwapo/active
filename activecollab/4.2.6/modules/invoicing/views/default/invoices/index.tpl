{title}Invoices{/title}
{add_bread_crumb}All Invoices{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}
{use_widget name="payment_container" module="payments"}

<div id="invoices">
	<div class="empty_content">
		<div class="objects_list_title">{lang}Invoices{/lang}</div>
		<div class="objects_list_icon"><img src="{image_url name='icons/48x48/invoicing.png' module=invoicing}" alt=""/></div>
		<div class="objects_list_details_actions">
        <ul>
            <li><a href="{assemble route='invoices_add'}" id="new_invoice">{lang}New Invoice{/lang}</a></li>
        </ul>
		</div>

    <div class="object_list_details_additional_actions">
      <a href="{assemble route='invoices_archive'}" id="view_archive"><span><img src="{image_url name="icons/12x12/archive.png" module="environment"}">{lang}Browse Archive{/lang}</span></a>
    </div>

		<div class="object_lists_details_tips">
		  <h3>{lang}Tips{/lang}:</h3>
		  <ul>
		    <li>{lang}To select a invoice and load its details, please click on it in the list on the left{/lang}</li>
		  </ul>
		</div>
	</div>
</div>

{include file=get_view_path('_initialize_objects_list', 'invoices', $smarty.const.INVOICING_MODULE)}