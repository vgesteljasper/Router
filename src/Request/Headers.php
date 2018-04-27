<?php namespace VanGestelJasper\Router\Request;

use \VanGestelJasper\Collection\Collection;

class Headers extends Collection
{

  /**
   * Request constructor.
   */
  public function __construct($headers)
  {
    // function to loop map over keys and values
    function array_map_assoc(callable $f, array $a) {
      return array_column(array_map($f, array_keys($a), $a), 1, 0);
    }

    // lowercase mapper function
    $toLowerCase = function($key, $value) {
      return [strtolower($key), $value];
    };

    // map header values to lowercase
    $headers = array_map_assoc($toLowerCase, $headers);

    // construct the collection
    parent::__construct($headers);
  }
}
