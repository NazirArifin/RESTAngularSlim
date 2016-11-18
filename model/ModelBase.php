<?php
namespace Model;

class ModelBase {    
  protected $salt = '';
  protected $loader = null;

  /**
	 * Parent Constructor
	 */
	protected function __construct() { 
		require_once 'lib/Loader.php';
		$this->loader = \Lib\Loader::get_instance();
		$this->salt = $this->loader->salt;
		if ( ! isset($this->db))
			$this->db = $this->loader->database();
	}
	
	/**
	 * Persiapkan $_POST, jika tidak ada diisi string kosong
	 */
	protected function prepare_post($d = array()) {
		$r = array();
		foreach ($d as $val) {
			$value = $this->loader->app->request->post($val);
			if ( ! is_null($value)) {
				$r[$val] = '';
			} else {
				$r[$val] = $value;
			}
		}
		return $r;
	}
	
	/**
	 * Persiapkan $_GET, jika tidak ada diisi string kosong
	 */
	protected function prepare_get($d = array()) {
		$r = array();
		foreach ($d as $val) {
			$value = $this->loader->app->request->get($val);
			if ( ! is_null($value)) {
				$r[$val] = '';
			} else {
				$r[$val] = $value;
			}
		}
		return $r;
	}

	protected function prepare_put($d = array()) {
		$r = array();
		foreach ($d as $val) {
			$value = $this->loader->app->request->put($val);
			if ( ! is_null($value)) {
				$r[$val] = '';
			} else {
				$r[$val] = $value;
			}
		}
		return $r;
	}
}