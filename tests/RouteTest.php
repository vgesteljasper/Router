<?php

use PHPUnit\Framework\TestCase;
use VanGestelJasper\Router\Route;

class RouteTest extends TestCase
{

  public function __construct()
  {
    parent::__construct();
    $this->collection = new Route('/projects/something', 'GET', ['slug'], 'TestHandler');
  }

  public function testUseMethodWorks()
  {
    $this->collection->use('TestMiddleware');
    $this->assertEquals(['TestMiddleware'], $this->collection->middleware);

    $this->collection->use('TestMiddlewareOne', 'TestMiddlewareTwo');
    $this->assertEquals(['TestMiddlewareOne', 'TestMiddlewareTwo'], $this->collection->middleware);
  }

}
