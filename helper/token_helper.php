<?php
/**
 * Save token
 */
function save_token($token, $exp) {
	setcookie('token', $token, $exp, '/', '', ( ! empty($_SERVER['HTTPS'])), TRUE);
}

/**
 * Delete token
 */
function delete_token() {
	setcookie('token', "", time() - 3600);
}

/**
 * Cek token
 */
function cek_token(&$ctr, $app = null) {
	if ( ! isset($_COOKIE['token'])) {
		return false;
	} else {
		$ctr->load('file', 'lib/JWT.php');
		$ctr->load('helper', 'date');
		$ctr->load('model', 'main');
		$token = $ctr->MainModel->validate_token($_COOKIE['token']);
		// hapus cookie token jika tidak valid
		if ($token === false) {
			delete_token($ctr);
			return false;
		} else {
			// tambah waktu expiration cookie
			$exp = time() + (5 * 24 * 3600);
			save_token($_COOKIE['token'], $exp);
			return $token;
		}
	}
}
