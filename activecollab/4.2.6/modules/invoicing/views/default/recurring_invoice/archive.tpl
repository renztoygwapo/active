{title}Archived Recurring Profiles{/title}
{add_bread_crumb}Archived Profiles{/add_bread_crumb}
{use_widget name="objects_list" module="environment"}

<div id="recurring_profile">

  <div class="empty_content">
      <div class="objects_list_title">{lang}Recurring Profiles Archive{/lang}</div>
      <div class="objects_list_icon"><img src="{image_url name='icons/48x48/recurring-profiles.png' module=invoicing}" alt=""/></div>

      <div class="object_list_details_additional_actions">
        <a href="{assemble route='recurring_profiles'}" id="view_archive">{lang}Browse Active{/lang}</a>
      </div>

      <div class="object_lists_details_tips">
        <h3>{lang}Tips{/lang}:</h3>
        <ul>
          <li>{lang}To select a recurring profile and load its details, please click on it in the list on the left{/lang}</li>
          <!--<li>{lang}It is possible to select multiple recurring profiles at the same time. Just hold Ctrl key on your keyboard and click on all the recurring profiles that you want to select{/lang}</li>-->
        </ul>
      </div>
  </div>
  
  <!--<div class="multi_content">
      <table>    
        <tr>
          <td class="checkbox"><label><input type="checkbox" value="change_project_client" />{lang}Change Client{/lang}</label></td>
          <td class="new_value">{select_company name=company_id user=$logged_user optional=true can_create_new=false}</td>
        </tr>
      </table>
  </div>-->
</div>

{include file=get_view_path('_initialize_objects_list', 'recurring_invoice', $smarty.const.INVOICING_MODULE)}
