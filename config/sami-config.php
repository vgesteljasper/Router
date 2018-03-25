<?php

define('ROOT', __DIR__.'/..');

$options = [
  'build_dir' => ROOT.'/docs',
  'cache_dir' => ROOT.'/cache',
];

return new Sami\Sami(ROOT.'/src', $options);
