{title}Document Archive{/title}
{add_bread_crumb}All{/add_bread_crumb}

<div id="documents">
  <div class="empty_content">
    <div class="objects_list_title">{lang}Document Archive{/lang}</div>
    <div class="objects_list_icon"><img src="{image_url name='icons/48x48/documents.png' module=documents}" alt=""/></div>
    <div class="object_list_details_additional_actions">
      <a href="{assemble route='documents'}" id="view_archive"><span>{lang}Browse Active{/lang}</span></a>
    </div>
    <div class="object_lists_details_tips">
      <h3>{lang}Tips{/lang}:</h3>
      <ul>
        <li>{lang}To select a document and load its details, please click on it in the list on the left{/lang}</li>
        <li>{lang}It is possible to select multiple documents at the same time. Just hold Ctrl key on your keyboard and click on all the documents that you want to select{/lang}</li>
      </ul>
    </div>
  </div>
</div>

{include file=get_view_path('_initialize_objects_list', 'documents', $smarty.const.DOCUMENTS_MODULE)}