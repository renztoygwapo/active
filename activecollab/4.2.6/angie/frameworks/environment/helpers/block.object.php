<?php

  /**
   * Object inspector
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Render object details
   * 
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_object($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    $object = array_required_var($params, 'object', true, 'ApplicationObject');
    $user = array_required_var($params, 'user', true, 'IUser');

    $current_request = $smarty->getVariable('request')->value;
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    $show_inspector = array_var($params, 'show_inspector', true, true);
    
    if(empty($params['id'])) {
      $params['id'] = $object->getBaseTypeName() . '_' . $object->getId() . '_details_' . (substr(time(), 6) . rand(0, 1000));
    } // if
    
    if(empty($params['class'])) {
      $params['class'] = 'object_wrapper';
    } else {
      $params['class'] .= ' object_wrapper';
    } // if

    $params['object_class'] = get_class($object);
    $params['object_id'] = $object->getId();
    $params['object_base_type'] = $object->getBaseTypeName();
    
    $inspector_id = $params['id'] . '_inspector';
    $inspector_classes = array('object_inspector', get_class($object->inspector()));
    
    $result = '<div class="' . $params['class'] . '" id="' . $params['id'] . '">';
    
    if($show_inspector && ($object instanceof IInspector)) {
      $object->inspector()->setEventScope($current_request->getEventScope());

      if ($current_request->isQuickViewCall()) {
        $object->inspector()->setRenderScope(IInspectorImplementation::RENDER_SCOPE_QUICK_VIEW);
      } // if

    	$object->inspector()->load($user, $interface); // load inspector for current interface
    	
    	$result.= '<div class="wireframe_content_wrapper first"><div class="' . implode(' ', $inspector_classes) . '" id="' . $inspector_id . '"></div></div>';
    } // if
    
    $result .= '<div class="object_content">' . trim($content) . '</div>';
    
    if($show_inspector && ($object instanceof IInspector)) {
      if ($current_request->isQuickViewCall()) {
        $object->inspector()->addTitlebarWidget('permalink', new PermalinkInspectorTitlebarWidget(), false);
      } // if

    	$result.= '<script type="text/javascript">$("#' . $inspector_id . '").objectInspector(' . JSON::encode($object->inspector()->describe($user, true, AngieApplication::getPreferedInterface())) . ')</script>';
    } // if

    $result.= '</div>';    
    
    return $result;
  } // smarty_block_object