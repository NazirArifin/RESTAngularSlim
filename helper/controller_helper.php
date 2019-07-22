<?php

/**
 * Controller helper
 * Dipanggil dari file Loader.php untuk persiapan file config/controller.php
 */
// ----------------------------------------------------------------
/**
 * Sukses app dengan header 200 (OK)
 */
function success200($app, $data = '')
{
  http_response($app, 200, (empty($data) ? array('message' => 'ok') : $data));
}

/**
 * Sukses app dengan header 201 (Resource Created)
 */
function success201($app, $data = '')
{
  http_response($app, 201, (empty($data) ? array('message' => 'resource created') : $data));
}

/**
 * Sukses app dengan header 204 (Resource Deleted)
 */
function success204($app, $data = '')
{
  http_response($app, 204, (empty($data) ? array('message' => 'resource deleted') : $data));
}

// ----------------------------------------------------------------
/**
 * Halt app dengan header 400 (Bad Request)
 */
function error400($app, $data = '')
{
  http_response($app, 400, (empty($data) ? array('message' => 'bad request') : $data));
}

/**
 * Halt app dengan header 401 (Unauthorized)
 */
function error401($app)
{
  http_response($app, 401, (empty($data) ? array('message' => 'unauthorized') : $data));
}

/*
 * Halt app dengan header 403 (Forbidden)
 */
function error403($app)
{
  http_response($app, 403, (empty($data) ? array('message' => 'forbidden') : $data));
}

/**
 * Halt app dengan header 404 (Not Found)
 */
function error404($app)
{
  http_response($app, 404, (empty($data) ? array('message' => 'not found') : $data));
}

/**
 * Halt app dengan header 405 (Method not allowed)
 */
function error405($app)
{
  http_response($app, 405, (empty($data) ? array('message' => 'method not allowed') : $data));
}

/**
 * Halt app dengan header 422 (Unprocessable Entity)
 */
function error422($app)
{
  http_response($app, 422, (empty($data) ? array('message' => 'unprocessable entity') : $data));
}

/**
 * menghasilkan output json ke browser
 * @param  object $app  instance dari aplikasi
 * @param  mixed $data   data yang akan dijadikan json
 * @return void
 */
function json_output($app, $data)
{
  http_response($app, 200, $data);
}

/**
 * fungsi umum untuk response http, semua jadi json
 * @param  object  $app    instance dari aplikasi
 * @param  integer $status [description]
 * @param  mixed   $data   array untuk dijadikan json
 * @return void
 */
function http_response($app, $status = 400, $data)
{
  $app->response->setStatus($status);
  $app->contentType('application/json');
  echo json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
  $app->stop();
}

/**
 * autentifikasi menggunakan middleware
 * @param  obj 		$app slim app instance
 * @param  obj 		$ctr controller instance
 * @return boolean      sukses atau gagal autentifikasi
 */
function authenticate($app, $ctr)
{ };
