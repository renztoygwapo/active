{title}Quotes{/title}
{add_bread_crumb}All Quotes{/add_bread_crumb}

<div id="quotes">
  <div class="empty_content">
      <div class="objects_list_title">{lang}Quotes{/lang}</div>
      <div class="objects_list_icon"><img src="{image_url name='icons/48x48/proposals.png' module=invoicing}" alt=""/></div>
      <div class="objects_list_details_actions">
          <ul>
              <li><a href="{assemble route='quotes_add'}" id="new_quote">{lang}New Quote{/lang}</a></li>
          </ul>
      </div>

    <div class="object_list_details_additional_actions">
      <a href="{assemble route='quotes_archive'}" id="view_archive"><span><img src="{image_url name="icons/12x12/archive.png" module="environment"}">{lang}Browse Archive{/lang}</span></a>
    </div>

      <div class="object_lists_details_tips">
        <h3>{lang}Tips{/lang}:</h3>
        <ul>
          <li>{lang}To select a quote and load its details, please click on it in the list on the left{/lang}</li>
        </ul>
      </div>
  </div>
</div>

{include file=get_view_path('_initialize_objects_list', 'quotes', $smarty.const.INVOICING_MODULE)}