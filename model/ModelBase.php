<?php
namespace Model;

class ModelBase {    
  protected $salt = '';
  protected $loader = null;
  protected $vars = array();

  /**
	 * Parent Constructor
	 */
	public function __construct($vars = array()) { 
		require_once 'lib/Loader.php';
		$this->loader = \Lib\Loader::get_instance();
		$this->salt = $this->loader->get_salt();
		if ( ! isset($this->db))
			$this->db = $this->loader->database();
		if ( ! empty($vars)) $this->vars = $vars;
	}
	
  protected function get_all_vars($type, $vars = array()) {
    if ( ! empty($vars)) {
      $r = array();
      foreach ($vars as $v) {
        switch ($type) {
          case 'get': 
            $r[$v] = $this->loader->app->request->get($v); break;
          case 'post':
            $r[$v] = $this->loader->app->request->post($v); break;
          case 'put':
            $r[$v] = $this->loader->app->request->put($v); break;
          default:
            $r[$v] = $this->loader->app->request->get($v); break;
        }
      }
      return $r;
    }
    return null;
  }

	public function get_vars($vars = array()) {
    return $this->get_all_vars('get', $vars);
  }

  public function post_vars($vars = array()) {
    return $this->get_all_vars('post', $vars);
  }

  public function put_vars($vars = array()) {
    return $this->get_all_vars('put', $vars);
  }
}