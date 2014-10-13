{title}Documents{/title}
{add_bread_crumb}All{/add_bread_crumb}

<div id="documents">
  <div class="empty_content">
      <div class="objects_list_title">{lang}Documents{/lang}</div>
      <div class="objects_list_icon"><img src="{image_url name='icons/48x48/documents.png' module=documents}" alt=""/></div>
      <div class="objects_list_details_actions">
        <ul>
          {if $can_add_documents}
            <li><a href="{assemble route='documents_upload_file'}" id="documents_upload_document">{lang}Upload Files{/lang}</a></li>
            <li><a href="{assemble route='documents_add_text'}" id="documents_new_text_document" >{lang}New Text Document{/lang}</a></li>
          {/if}
          {if $manage_categories_url}<li><a href="{$manage_categories_url}" class="manage_objects_list_categories" title="{lang}Manage Document Categories{/lang}">{lang}Manage Categories{/lang}</a></li>{/if}
        </ul>
      </div>
      {if $can_manage_documents}
        <div class="object_list_details_additional_actions">
          <a href="{assemble route='documents_archive'}" id="view_archive"><span><img src="{image_url name="icons/12x12/archive.png" module="environment"}">{lang}Browse Archive{/lang}</span></a>
        </div>
      {/if}
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