<?php

/**
 * Introduce alternative user addresses
 *
 * @package angie.migrations
 */
class MigrateIntroduceWhitelistedTagsConfig extends AngieModelMigration {

	/**
	 * Migrate up
	 */
	function up() {
		$config_option_name = 'whitelisted_tags';
		$whitelisted_tags = $this->getConfigOptionValue($config_option_name);
		$whitelisted_tags['visual_editor'] = array(
			'p' => array('class', 'style'),
			'img' => array('image-type', 'object-id', 'class'),
			'strike' => array('class', 'style'),
			'span' => array('class', 'data-redactor-inlinemethods', 'data-redactor'),
			'a' => array('class', 'href'),
			'blockquote' => null,
			'br' => null,
			'b' => null, 'strong' => null,
			'i' => null, 'em' => null,
			'u' => null
		);
		$this->setConfigOptionValue($config_option_name, $whitelisted_tags);
	} // up

}