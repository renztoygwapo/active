{title}Master Categories{/title}

<div id="category_definitions">
{if is_foreachable($category_definitions)}
  {form action=Router::assemble('admin_settings_categories')}
    <div class="content_stack_wrapper">
      {foreach $category_definitions as $category_definition}
        <div class="content_stack_element">
          <div class="content_stack_element_info">
            <h3>{$category_definition.label}</h3>
          </div>
          
          <div class="content_stack_element_body">
            {string_list name=$category_definition.name value=$category_definition.value link_title='Add a Category'}
          </div>
        </div>
      {/foreach}
    </div>
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
{else}
  <p>{lang}There are no master category sets defined in the database{/lang}!</p>
{/if}
</div>