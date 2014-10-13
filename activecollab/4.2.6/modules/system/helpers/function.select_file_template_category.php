<?php

	/**
	 * select_file_template_category helper implementation
	 *
	 * @package activeCollab.modules.files
	 * @subpackage helpers
	 */

	/**
	 * Render select file template category helper
	 *
	 * @param $params
	 * @param $smarty
	 * @return string
	 * @throws InvalidInstanceError
	 */
	function smarty_function_select_file_template_category($params, &$smarty) {
		AngieApplication::useHelper('select_category', CATEGORIES_FRAMEWORK);

		$parent = array_var($params, 'parent');
		if(!($parent instanceof ProjectTemplate)) {
			throw new InvalidInstanceError('parent', $parent, '$parent is expected to be ProjectTemplate instance');
		} // if

		$user = array_var($params, 'user');
		if(!($user instanceof User)) {
			throw new InvalidInstanceError('user', $user, '$user is expected to be User instance');
		} // if

		if(array_var($params, 'can_create_new', true) && ProjectObjectTemplates::canAdd($user)) {
			$params['add_url'] = Router::assemble('project_object_template_add', array('template_id' => $parent->getId(), 'object_type' => 'category', 'category_type' => 'file'));
		} // if

		$params['type'] = 'Category';

		$interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
		$parent = array_var($params, 'parent', null, true);
		$category_type = array_var($params, 'type', null, true);

		$label_type = strtolower(array_var($params, 'label_type', null, true));
		if ($label_type == 'inner') {
			$control_label = array_var($params, 'label', null, true);
		} else {
			$control_label = null;
		} // if

		if(isset($params['user'])) {
			unset($params['user']);
		} // if

		if(empty($params['id'])) {
			$params['id'] = HTML::uniqueId('select_category');
		} // if

		if(isset($params['class']) && $params['class']) {
			$params['class'] .= ' select_category';
		} else {
			$params['class'] = 'select_category';
		} // if

		$name = array_var($params, 'name', null, true);
		$value = array_var($params, 'value', null, true);

		// Prepare options
		$options = array();

		$categories = ProjectObjectTemplates::findBySQL('SELECT * FROM ' . TABLE_PREFIX . 'project_object_templates WHERE template_id = ? AND subtype = ?', $parent->getId(), 'file');

		if(is_foreachable($categories)) {
			foreach($categories as $category) {
				if ($category instanceof ProjectObjectTemplate) {
					$options[] = HTML::optionForSelect($category->getValue('name'), $category->getId(), $category->getId() == $value, array(
						'class' => 'object_option',
					));
				} // if
			} // foreach
		} // if

		// Default interface
		if($interface == AngieApplication::INTERFACE_DEFAULT) {
			$add_url = array_var($params, 'add_url', false, true);

			if($add_url) {
				$js_options = JSON::encode(array(
					'add_object_url' => $add_url,
					'object_name' => 'category',
					'add_object_message' => lang('Please insert new file category name'),
					'on_new_object' => isset($params['on_new_category']) ? $params['on_new_category'] : null,
					'success_event' => isset($params['success_event']) ? $params['success_event'] : null,
					'additional_event_params' => array(
						'context'  => $parent instanceof ApplicationObject ? ($parent->fieldExists('id') ? get_class($parent) . '_' . $parent->getId() : get_class($parent)) : null,
						'type'     => $category_type
					)
				));
			} else {
				$js_options = '{}';
			} // if
		} // if

		if ($control_label) {
			if (array_var($params, 'optional', true, true)) {
				$options = array_merge(array(
					HTML::optionForSelect(lang('No Category')),
					HTML::optionForSelect(''),
				), $options);
			} // if
			return HTML::select($name, HTML::optionGroup($control_label, $options, array('class' => 'centered')), $params);
		} else {
			$result = array_var($params, 'optional', true, true) ?
				HTML::optionalSelect($name, $options, $params, lang('None')) :
				HTML::select($name, $options, $params);
		} // if

		if($interface == AngieApplication::INTERFACE_DEFAULT) {
			AngieApplication::useWidget('select_named_object', ENVIRONMENT_FRAMEWORK);
			$result .= '<script type="text/javascript">$("#' . $params['id'] . '").selectNamedObject("init", ' . $js_options . ');</script>';
		} // if

		return $result;
	} // smarty_function_select_file_template_category