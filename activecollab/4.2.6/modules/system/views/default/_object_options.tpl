<ul class="object_options" id="{$_object_options_id}">
{foreach from=$_object_options key=_quick_option_name item=_quick_option}
  <li class="{if isset($_quick_option.class)}{$_quick_option.class}{/if} {if $_quick_option.quick < 1}less_important{/if}" id="object_quick_option_{$_quick_option_name}">{link href=$_quick_option.url method=$_quick_option.method confirm=$_quick_option.confirm not_lang=yes}<span>{$_quick_option.text}</span>{/link}</li>
{/foreach}
</ul>