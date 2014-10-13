<?php

  /**
   * Favorites framework initialization file
   * 
   * @package angie.frameworks.favorites
   */

  const FAVORITES_FRAMEWORK = 'favorites';
  const FAVORITES_FRAMEWORK_PATH = __DIR__;

  defined('FAVORITES_FRAMEWORK_INJECT_INTO') or define('FAVORITES_FRAMEWORK_INJECT_INTO', 'system');
  defined('FAVORITES_FRAMEWORK_DEFINE_ROUTES') or define('FAVORITES_FRAMEWORK_DEFINE_ROUTES', false);

  AngieApplication::setForAutoload(array(
    'FwFavorites' => FAVORITES_FRAMEWORK_PATH . '/models/FwFavorites.class.php', 
    'IUserFavoritesImplementation' => FAVORITES_FRAMEWORK_PATH . '/models/IUserFavoritesImplementation.class.php', 
    'ICanBeFavorite' => FAVORITES_FRAMEWORK_PATH . '/models/ICanBeFavorite.class.php',
  	'FavoriteInspectorIndicator' => FAVORITES_FRAMEWORK_PATH .'/models/FavoriteInspectorIndicator.class.php' 
  ));