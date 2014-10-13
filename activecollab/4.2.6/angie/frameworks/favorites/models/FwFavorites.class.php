<?php

  /**
   * Framework level favorites manager
   * 
   * @package angie.framework.favorites
   * @subpackage models
   */
  abstract class FwFavorites {
    
    /**
     * Returns true if $parent is marked as favorite by $user
     * 
     * $parent can be ICanBeFavorite instance or an array where first element is 
     * class name and second parameter is parent ID
     * 
     * @param mixed $parent
     * @param User $user
     * @return boolean
     * @throws InvalidParamError
     */
    static function isFavorite($parent, User $user) {
      if($parent instanceof ICanBeFavorite) {
        $parent_type = get_class($parent);
        $parent_id = $parent->getId();
      } elseif(is_array($parent) && count($parent) == 2) {
        list($parent_type, $parent_id) = $parent;
      } else {
        throw new InvalidParamError('$parent', $parent, '$parent should be an instance of ICanBeFavorite class or an array');
      } // if
      
      return in_array("$parent_type-$parent_id", self::getUserCache($user));
    } // isFavorite
    
    /**
     * Add parent to favorites for $user
     * 
     * @param ICanBeFavorite $parent
     * @param User $user
     */
    static function addToFavorites(ICanBeFavorite $parent, User $user) {
      DB::execute('REPLACE INTO ' . TABLE_PREFIX . 'favorites (parent_type, parent_id, user_id) VALUES (?, ?, ?)', get_class($parent), $parent->getId(), $user->getId());

      AngieApplication::cache()->removeByObject($parent);
      AngieApplication::cache()->removeByObject($user, 'favorites');
    } // addToFavorites
    
    /**
     * Remove $parent from user's favorites
     * 
     * @param ICanBeFavorite $parent
     * @param User $user
     */
    static function removeFromFavorites(ICanBeFavorite $parent, User $user) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'favorites WHERE parent_type = ? AND parent_id = ? AND user_id = ?', get_class($parent), $parent->getId(), $user->getId());

      AngieApplication::cache()->removeByObject($parent);
      AngieApplication::cache()->removeByObject($user, 'favorites');
    } // removeFromFavorites
    
    /**
     * Rebuild favorites cache for given user
     * 
     * @param User $user
     * @param boolean $refresh
     * @return array
     */
    static protected function getUserCache(User $user, $refresh = false) {
      return AngieApplication::cache()->getByObject($user, 'favorites', function() use ($user) {
        $result = array();

        $rows = DB::execute('SELECT parent_type, parent_id FROM ' . TABLE_PREFIX . 'favorites WHERE user_id = ?', $user->getId());
        if($rows) {
          foreach($rows as $row) {
            $result[] = "$row[parent_type]-$row[parent_id]";
          } // foreach
        } // if

        return $result;
      }, $refresh);
    } // getUserCache
    
    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
    
    /**
     * Return ID-s by user and type
     * 
     * @param IUser $user
     * @param mixed $type
     * @return array
     */
    static function findIdsByUserAndType(IUser $user, $type) {
      return DB::executeFirstColumn('SELECT parent_id FROM ' . TABLE_PREFIX . 'favorites WHERE user_id = ? AND parent_type = ?', $user->getId(), $type);
    } // findIdsByUserAndType
    
    /**
     * Return object ID-s by parent types
     * 
     * @param IUser $user
     * @param array $types
     * @return array
     */
    static function findIdsByUserAndTypes(IUser $user, $types) {
      $rows = DB::execute('SELECT parent_type, parent_id FROM ' . TABLE_PREFIX . 'favorites WHERE user_id = ? AND parent_type IN (?)', $user->getId(), $types);
        
      if($rows) {
        $result = array();
        
        foreach($rows as $row) {
          if(isset($result[$row['parent_type']])) {
            $result[$row['parent_type']][] = (integer) $row['parent_id'];
          } else {
            $result[$row['parent_type']] = array((integer) $row['parent_id']);
          } // if
        } // foreach
        
        return $result;
      } else {
        return null;
      } // if
    } // findIdsByUserAndTypes
    
    /**
     * Find by user
     * 
     * @param User $user
     * @return DBResult
     */
    static function findFavoriteObjectsByUser(User $user) {
      $favorites = DB::execute('SELECT parent_id, parent_type FROM ' . TABLE_PREFIX . 'favorites WHERE user_id = ?', $user->getId());
      
      if ($favorites) {
        $result = array();
        foreach ($favorites as $favorite) {
          try {
            $object_id = $favorite['parent_id'];
            $object_type = $favorite['parent_type'];

            if (!($object_id && $object_type)) {
              throw new Error(lang('Favorite requires parent id and parent type'));
            } // if

            $object = DataObjectPool::get($object_type, $object_id);

            if (method_exists($object, 'canView')) {
              if ($object->canView($user)) {
                if ($object instanceof IState && method_exists($object, 'getState')) {
                  if ($object->getState() >= STATE_ARCHIVED) { // avoid deleted objects
                    $result[] = $object;
                  } // else nothing!
                } else {
                  $result[] = $object;
                } // if
              } // if
            } else {
              $result[] = $object;
            } // if
          } catch (Exception $e) {
            continue; // skip item
          } // try
        } // foreach

        usort($result, function($a, $b) {
          return strcmp(strtolower($a->getName()), strtolower($b->getName()));
        });

        return $result;
      } // if

      return null;
    } // findByUser
    
    // ---------------------------------------------------
    //  Mass-management
    // ---------------------------------------------------
  
    /**
     * Drop all records by user
     *
     * @param User $user
     */
    static function deleteByUser(User $user) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'favorites WHERE user_id = ?', $user->getId());
    } // deleteByUser
    
    /**
     * Drop records by object
     *
     * @param ICanBeFavorite $parent
     */
    static function deleteByParent(ICanBeFavorite $parent) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'favorites WHERE parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId());
    } // deleteByObject
    
    /**
     * Delete entries by parents
     * 
     * $parents is an array where key is parent type and value is array of 
     * object ID-s of that particular parent
     * 
     * @param array $parents
     */
    static function deleteByParents($parents) {
      if(is_foreachable($parents)) {
        foreach($parents as $parent_type => $parent_ids) {
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'favorites WHERE parent_type = ? AND parent_id IN (?)', $parent_type, $parent_ids);
        } // foreach
      } // if
    } // deleteByParents
    
    /**
     * Delete by parent types
     * 
     * @param array $types
     */
    static function deleteByParentTypes($types) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'favorites WHERE parent_type IN (?) ', $types);
    } // deleteByParentTypes
    
  }