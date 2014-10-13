{use_widget name='ui_sortable' module="environment"}
<div id="reorder_milestones">
{if $grouped_milestones}
  {form action=$reorder_milestones_url}
    {wrap_fields}
      <p id="reorder_milestones_node"><b>{lang}Important{/lang}:</b> {lang}You can change position of milestones that have the same start date, but you can't move milestones between dates. To change the start date, close this dialog and reschedule milestones using drag and drop on the timeline{/lang}.</p>

      {foreach $grouped_milestones as $group}
      <div class="reorder_milestones_group">
        <h3>{$group.label}</h3>
        <ul>
        {foreach $group.milestones as $milestone}
          <li milestone_id="{$milestone->getId()}"><span class="milestone_name">{$milestone->getName()}</li>
        {/foreach}
        </ul>
      </div>
      {/foreach}
    {/wrap_fields}

    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
{else}
  <p class="empty_page">{lang}Nothing to reorder{/lang}</p>
{/if}
</div>

<script type="text/javascript">
  $('#reorder_milestones').each(function() {
    var wrapper = $(this);
    var form = wrapper.find('form');

    var update_hidden_inputs = function() {
      form.find('input.milestone_position').remove();

      wrapper.find('div.reorder_milestones_group').each(function() {
        var counter = 0;

        $(this).find('li').each(function() {
          form.append('<input type="hidden" name="milestones[' + $(this).attr('milestone_id') + ']" value="' + counter++ + '" class="milestone_position">');
        });
      });
    };

    wrapper.find('div.reorder_milestones_group').each(function() {
      $(this).sortable({
        'axis' : 'y',
        'cursor': 'move',
        'items' : 'li',
        'revert' : false,
        'update' : function() {
          update_hidden_inputs();
        }
      });
    });

    // Initial input values
    update_hidden_inputs();
  });
</script>