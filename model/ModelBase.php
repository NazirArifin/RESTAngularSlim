<?php
namespace Model;

class ModelBase {    
  protected $salt = '';
  protected $loader = null;
  protected $vars = array();
  protected $is_sanitized = false;

  /**
	 * Parent Constructor
	 */
	protected function __construct($vars = array()) { 
		require_once 'lib/Loader.php';
		$this->loader = \Lib\Loader::get_instance();
		$this->salt = $this->loader->get_salt();
		if ( ! isset($this->db))
			$this->db = $this->loader->database();
		if ( ! empty($vars)) $this->vars = $vars;
	}
	
	protected function sanitize_input() {

	}

	protected function get_input($key = '') {
		if ( ! $this->is_sanitized) $this->sanitize_input();
		if (empty($key)) return $this->vars;
		else return (isset($this->vars[$key]) ? $this->vars[$key] : '');
	}
}