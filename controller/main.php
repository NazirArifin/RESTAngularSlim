<?php
$app->options('/', function() use($app) { $app->status(200); $app->stop(); });
$app->get('/', function() use($app, $ctr) {
	
  $_SESSION['jenis'] = 'admin';
  if ($_SESSION['jenis'] == 'admin') 
    $ctr->view('login.html', array());
  
});