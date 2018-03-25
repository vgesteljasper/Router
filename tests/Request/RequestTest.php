<?php

use PHPUnit\Framework\TestCase;
use VanGestelJasper\Router\Request\Request;

class RequestTest extends TestCase
{

  public function __construct()
  {
    parent::__construct();

    $_SERVER['HTTP_HOST'] = 'localhost:8080';
    $_SERVER['REQUEST_URI'] = '/project/example';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $this->request = new Request();
  }

  public function testRequestCanConstruct()
  {
    $this->assertObjectHasAttribute('url', $this->request);
    $this->assertObjectHasAttribute('path', $this->request);
    $this->assertObjectHasAttribute('method', $this->request);

    $this->assertEquals('https://localhost:8080/project/example', $this->request->url);
    $this->assertEquals('/project/example', $this->request->path);
    $this->assertEquals('GET', $this->request->method);
  }

}
