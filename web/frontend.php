<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

// Use APC for autoloading to improve performance
// Change 'sf2' by the prefix you want in order to prevent key conflict with another application
/*
$loader = new ApcClassLoader('sf2', $loader);
$loader->register(true);
*/

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
$request = Request::createFromGlobals();

require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../../app_front/FrontCache.php';
$kernel = new AppKernel('prod', false);
$kernel->setApplication('frontend');
$kernel->loadClassCache();
//$kernel = new FrontCache($kernel);

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
