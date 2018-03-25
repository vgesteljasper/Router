<?php

use VanGestelJasper\Router\Request\Query;

class QueryTest extends CollectionTest
{

  public function __construct()
  {
    parent::__construct();

    $this->collection = new Query(['keyOne' => 'valueOne', 'keyTwo' => 'valueTwo']);
  }

}
