<?php

  /**
   * Class that represents TEXT database columns
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBTextColumn extends DBColumn {
    
    /**
     * Text fields have size
     *
     * @var boolean
     */
    protected $has_size = true;
    
    /**
     * Text columns can't have default value
     * 
     * @var boolean
     */
    protected $has_default = false;
    
    /**
     * Construct without default value
     *
     * @param string $name
     */
    function __construct($name) {
      parent::__construct($name);
    } // __construct
    
    /**
     * Create and return tme column
     *
     * @param string $name
     * @return DBTextColumn
     */
    static public function create($name) {
      return new DBTextColumn($name);
    } // create
    
    /**
     * Return type definition string
     *
     * @return string
     */
    function prepareTypeDefinition() {
      switch($this->size) {
        case self::BIG:
          return 'longtext';
        case self::SMALL:
        case self::NORMAL:
          return 'text';
        default:
          return $this->size . 'text';
      } // if
    } // prepareTypeDefinition
    
    /**
     * Return model definition code for this column
     *
     * @return string
     */
    function prepareModelDefinition() {
      if($this->name == 'raw_additional_properties') {
        return 'DBAdditionalPropertiesColumn::create()';
      } else {
        $result = "DBTextColumn::create('" . $this->getName() ."')";
      
        if($this->getSize() != DBColumn::NORMAL) {
          switch($this->getSize()) {
            case DBColumn::TINY:
              $result .= '->setSize(DBColumn::TINY)';
              break;
            case DBColumn::SMALL:
              $result .= '->setSize(DBColumn::SMALL)';
              break;
            case DBColumn::MEDIUM:
              $result .= '->setSize(DBColumn::MEDIUM)';
              break;
            case DBColumn::BIG:
              $result .= '->setSize(DBColumn::BIG)';
              break;
          } // if
        } // if
        
        return $result;
      } // if
    } // prepareModelDefinition
    
  }

?>