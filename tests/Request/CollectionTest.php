<?php

use PHPUnit\Framework\TestCase;
use VanGestelJasper\Router\Request\Collection;

class CollectionTest extends TestCase
{

  public $collection;

  public function __construct()
  {
    parent::__construct();

    $this->collection = new Collection(['keyOne' => 'valueOne', 'keyTwo' => 'valueTwo']);
  }

  public function testCollectionCanConstruct()
  {
    $this->assertEquals('valueOne', $this->collection->keyOne);
    $this->assertEquals('valueTwo', $this->collection->keyTwo);
  }

  public function testHasMethodWorks()
  {
    $this->assertTrue($this->collection->has('keyOne'));
  }

  public function testGetMethodWorks()
  {
    $this->assertEquals('valueOne', $this->collection->get('keyOne'));
  }

  public function testAddMethodWorks()
  {
    $this->collection->add('keyThree', 'valueThree');
    $this->assertTrue($this->collection->has('keyThree'));
    $this->assertEquals('valueThree', $this->collection->get('keyThree'));
  }

  public function testRemoveMethodWorks()
  {
    $this->collection->remove('keyTwo');
    $this->assertObjectNotHasAttribute('keyTwo', $this->collection);
  }

}
