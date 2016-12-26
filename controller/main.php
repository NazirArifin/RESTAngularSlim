<?php


$app->options('/', function() use($app) { $app->status(200); $app->stop(); });
$app->get('/', function() use($app, $ctr) {
	
  $ctr->view('index', array('nama' => 'Web Developer'));
  $ctr->model('main');
  
  
});

$app->get('/hello/{name}', function($request, $response, $args) use($app, $ctr) {

  var_dump($args);

});