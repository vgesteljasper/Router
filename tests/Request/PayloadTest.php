<?php

use VanGestelJasper\Router\Request\Payload;

class PayloadTest extends CollectionTest
{

  public function __construct()
  {
    parent::__construct();

    $this->collection = new Payload(['keyOne' => 'valueOne', 'keyTwo' => 'valueTwo']);
  }

}
