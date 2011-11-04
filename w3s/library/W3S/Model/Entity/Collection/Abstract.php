<?php

abstract class W3S_Model_Entity_Collection_Abstract implements SeekableIterator, ArrayAccess, Countable {

  /**
   * Default entity class name
   *
   * @var string
   */
  protected $_entityClass = 'W3S_Model_Entity';

  /**
   * @var integer
   */
  protected $_pointer = 0;

  /**
   * @var integer
   */
  protected $_count;

  /**
   * @var array
   */
  protected $_data = array();

  /**
   * @var array
   */
  protected $_entities = array();

  /**
   * @param  array  $data
   * @param  string $entityClass
   * @return void
   */
  public function __construct($data, $entityClass = null) {
    if ($entityClass) {
      $this->_entityClass = $entityClass;
    }
    $this->_data = $data;
    $this->_count = count($this->_data);
  }

  /**
   * @return Model_Entity_CollectionAbstract
   */
  public function rewind() {
    $this->_pointer = 0;

    return $this;
  }

  /**
   * @return Model_EntityAbstract
   */
  public function current() {
    if ($this->valid() === false) {
      return;
    } elseif (empty($this->_entities[$this->_pointer])) {
      $this->_entities[$this->_pointer] = new $this->_entityClass($this->_data[$this->_pointer]);
    }
    return $this->_entities[$this->_pointer];
  }

  /**
   * @return integer
   */
  public function key() {
    return $this->_pointer;
  }

  /**
   * @return Model_Entity_CollectionAbstract
   */
  public function next() {
    ++$this->_pointer;

    return $this;
  }

  /**
   * @return boolean
   */
  public function valid() {
    return $this->_pointer < $this->_count;
  }

  /**
   * @return integer
   */
  public function count() {
    return $this->_count;
  }

  /**
   * @param  integer                         $position
   * @return Model_Entity_CollectionAbstract
   * @throws InvalidArgumentException
   */
  public function seek($position) {
    $position = (int) $position;
    if ($position < 0 || $position > $this->_count) {
      throw new InvalidArgumentException(sprintf('Illegal seek index "%s" provided', $position));
    }
    $this->_pointer = $position;

    return $this;
  }

  /**
   * @param  integer $offset
   * @return boolean
   */
  public function offsetExists($offset) {
    return array_key_exists((int) $offset, $this->_data);
  }

  /**
   * @param  integer              $offset
   * @return Model_EntityAbstract
   */
  public function offsetGet($offset) {
    $this->_pointer = (int) $offset;

    return $this->current();
  }

  /**
   * @param  integer $offset
   * @param  mixed   $value
   * @return void
   */
  public function offsetSet($offset, $value) {
    
  }

  /**
   * @param  integer $offset
   * @return void
   */
  public function offsetUnset($offset) {
    
  }

  /**
   * @param  integer              $position
   * @param  boolean              $seek
   * @return Model_EntityAbstract
   */
  public function getEntity($position, $seek = false) {
    $key = $this->key();
    $this->seek($position);
    $entity = $this->current();

    if ($seek === false) {
      $this->seek($key);
    }
    return $entity;
  }

  /**
   * @return array
   */
  public function toArray() {
    $returnData = array();
    $this->rewind();
    while ($entity = $this->current()) {
      $returnData[] = $entity->toArray();
      $this->next();
    }
    return $returnData;
  }

}