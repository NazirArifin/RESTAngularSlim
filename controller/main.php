<?php
// ----------------------------------------------------------------
/**
 * Method: GET
 * Verb: 
 */
$app->options('/', function() use($app) { $app->status(200); $app->stop(); });
$app->get('/', function() use($app, $ctr) {
	$ctr->load('model', 'admin_mains');
	$r = $ctr->AdminMainModel->cds();
	$ctr->load('view', 'index.html', array(
		'greeting' 	=> 'Hai.. selamat datang!',
		'server'	=> 'http://server.me',
		'cds'		=> $r
	));
});

// ----------------------------------------------------------------
/**
 * Method: GET
 * Verb: cds
 */
$app->options('/cds', function() use($app) { $app->status(200); $app->stop(); });
$app->get('/cds', function() use($app, $ctr) {
	$ctr->load('model', 'admin_main');
	$r = $ctr->AdminMainModel->cds();
	return json_output($app, $r);
});
