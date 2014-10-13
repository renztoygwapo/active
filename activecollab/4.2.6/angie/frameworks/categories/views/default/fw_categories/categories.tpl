<div class="manage_categories">
  <div class="manage_categories_categories">
    <table class="common" style="display: none">
      <tbody>
    {if is_foreachable($categories)}
      {foreach from=$categories item=category}
        <tr class="category" category_id="{$category->getId()}">
          <td class="name">{$category->getName()}</td>
          <td class="options">{if $category->canEdit($logged_user)}{link href=$category->getEditUrl() title='Rename' class=rename_category title="Rename Category"}<img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="edit" />{/link}{/if}{if $category->canDelete($logged_user)}{link href=$category->getDeleteUrl() class=delete_category title="Delete Category"}<img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="" />{/link}{/if}</td>
        </tr>
      {/foreach}
    {/if}
      </tbody>
    </table>
    <p class="empty_page" style="display: none"><span class="inner">{lang}There are no categories in this section!{/lang}</span></p>
  </div>
  
  <div class="new_category_button">
    {link_button href=$add_category_url label="New Category" icon_class=button_add class='new_category'}
  </div>
</div>