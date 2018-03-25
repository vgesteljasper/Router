<?php

use VanGestelJasper\Router\Request\Headers;

class HeadersTest extends CollectionTest
{

  public function __construct()
  {
    parent::__construct();

    $this->collection = new Headers(['keyOne' => 'valueOne', 'keyTwo' => 'valueTwo']);
  }

}
