<?php


$app->options('/', function() use($app) { $app->status(200); $app->stop(); });
$app->get('/', function() use($app, $ctr) {
	
  $ctr->view('index', array('nama' => 'Web Developer'));
  
});

$app->get('/hello/:name', function($name) use($app, $ctr) {

  echo 'Hello ', $name;

});