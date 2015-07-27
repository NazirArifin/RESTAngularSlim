<?php
// ----------------------------------------------------------------
/**
 * Method: GET
 * Verb: 
 */
$app->options('/', function() use($app) { $app->status(200); $app->stop(); });
$app->get('/', function() use($app, $ctr) {
	$ctr->load('model', 'main');
	$r = $ctr->MainModel->get_cds();
	$ctr->load('view', 'index.html', array(
		'greeting' 	=> 'Hai.. selamat datang!',
		'server'	=> 'http://localhost',
		'cds'		=> $r
	));
});