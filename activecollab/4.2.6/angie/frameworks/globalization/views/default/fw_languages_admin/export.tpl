<language>
  <info>
    <ac_version><![CDATA[{$ac_version}]]></ac_version>
    <name><![CDATA[{$active_language->getName()}]]></name>
    <locale><![CDATA[{$active_language->getLocale()}]]></locale>
  </info>
  {if is_foreachable($translations)}
  <translations>
  {foreach from=$translations item=translation key=phrase}
    <translation phrase="{$phrase}"><![CDATA[{$translation}]]></translation>
  {/foreach}
  </translations>
  {/if}
</language>