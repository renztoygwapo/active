<ul class="object_options">
{foreach from=$_quick_options key=_quick_option_name item=_quick_option}
  <li {if isset($_quick_option.class)}class="{$_quick_option.class}"{/if} id="object_quick_option_{$_quick_option_name}">{link href=$_quick_option.url method=$_quick_option.method confirm=$_quick_option.confirm not_lang=yes}<span>{$_quick_option.text}</span>{/link}</li>
{/foreach}
</ul>