{if !$edit_mode}
      <li class="{$subobject->getVerboseType()|strtolower}_container outline_level">
{/if}
        <span class="row {$subobject->getVerboseType()|strtolower}_row" type="{$subobject->getVerboseType()|strtolower}">
          <input type="checkbox" class="checkbox"/>
          <a class="object_name collapsed {$subobject->getVerboseType()|strtolower}" subobjects_url="{assemble route=project_outline_subobjects object_id=$subobject->getId() project_slug=$active_project->getSlug()}"><span class="object_type">{lang}{$subobject->getVerboseType()}{/lang}</span>{$subobject->getName()}</a>
          
          <span class="responsible"><span class="responsible_inner">
            {if (($subobject instanceof Task) || ($subobject instanceof Subtask) || ($subobject instanceof Milestone))}
              {assign var=responsible value=$subobject->assignees()->getAssignee()}
              {if ($responsible instanceof User)}
                {user_link user=$responsible}
              {else}
                -
              {/if}
            {/if}
          </span></span>
          
          <span class="scheduled"><span class="scheduled_inner">
            {if (($subobject instanceof Task) || ($subobject instanceof Subtask) || ($subobject instanceof Milestone))}
              {if $subobject->getDueOn()}
                {$subobject->getDueOn()|date}
              {else}
                -
              {/if}
            {/if}          
          </span></span>
          
          <ol class="actions">
            {if ($subobject instanceof Task) || ($subobject instanceof TodoList)}
              <li><a href="{assemble route=project_outline_add_subtask project_slug=$active_project->getSlug() parent_id=$subobject->getId() skip_layout=1 async=1 outline=1}" class="add add_subtask" title="{lang}New Subtask{/lang}">{lang}Subtask{/lang}</a></li>
            {/if}
            
            {if ($subobject instanceof Milestone)}
              <li><a href="{assemble route=project_tasks_add project_slug=$active_project->getSlug() milestone_id=$subobject->getId() outline=1 async=1 skip_layout=1}" class="add add_task" title="{lang}New Task{/lang}">{lang}Task{/lang}</a></li>
              <li><a href="{assemble route=project_todo_lists_add project_slug=$active_project->getSlug() milestone_id=$subobject->getId() outline=1 async=1 skip_layout=1}" class="add add_todo_list" title="{lang}New Todo List{/lang}">{lang}Todo List{/lang}</a></li>
              <li><a href="{assemble route=project_milestone_reschedule project_slug=$active_project->getSlug() milestone_id=$subobject->getId() outline=1 async=1 skip_layout=1}" class="reschedule" title="{lang}Reschedule{/lang}">{lang}Reschedule{/lang}</a></li>
            {/if}
            
            {if ($subobject instanceof Subtask)}
              {if $subobject->canEdit($logged_user)}<li><a href="{assemble route=project_outline_edit_subtask project_slug=$active_project->getSlug() parent_id=$subobject->getParent()->getId() subtask_id=$subobject->getId() outline=1 async=1 skip_layout=1}" class="edit" title="{lang}Edit{/lang}">{lang}Edit{/lang}</a></li>{/if}
              {if $subobject->canDelete($logged_user)}<li><a href="{$subobject->state()->getTrashUrl()}" class="trash" title="{lang}Delete{/lang}">{lang}Delete{/lang}</a></li>{/if}
            {else}
              {if $subobject->canEdit($logged_user)}<li><a href="{$subobject->getEditUrl()}" class="edit" title="{lang}Edit{/lang}">{lang}Edit{/lang}</a></li>{/if}
              {if $subobject->state()->canTrash($logged_user)}<li><a href="{$subobject->state()->getTrashUrl()}" class="trash" title="{lang}Trash{/lang}">{lang}Trash{/lang}</a></li>{/if}
            {/if}
          </ol>
        </span>
{if !$edit_mode}
      </li>
{/if}