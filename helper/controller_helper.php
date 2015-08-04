<?php
/**
 * Controller helper
 * Dipanggil dari file Loader.php untuk persiapan file config/controller.php
 */

// ----------------------------------------------------------------
/**
 * Halt app dengan header 400 (Bad Request)
 */
function halt400($app) {
	$app->halt(400);
	$app->stop();
}
 
/**
 * Halt app dengan header 401 (Unauthorized)
 */
function halt401($app) {
	$app->halt(401);
	$app->stop();
}

/**
 * Halt app dengan header 403 (Forbidden)
 */
function halt403($app) {
	$app->halt(403);
	$app->stop();
}

/**
 * Halt app dengan header 404 (Not Found)
 */
function halt404($app) {
	$app->halt(404);
	$app->stop();
}

/**
 * Redirect ke home
 */
function redirect_home($app, $message = '') {
	$app->redirect('/home' . ( ! empty($message) ? '?pesan=' . $message : ''));
}

/**
 * Redirect ke root
 */
function redirect_index($app, $message = '') {
	$app->redirect('/' . ( ! empty($message) ? '?pesan=' . $message : ''));
}
 
/**
 * Header json
 */
function json_output($app, $data) {
	$app->contentType('application/json');
	echo json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
}