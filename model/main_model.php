<?php
/**
 * Main Model
 */
namespace Model;

class MainModel extends ModelBase {
	public function __construct() {
		parent::__construct();
	}
	
	private $salt 	= '';
	
	/**
	 * User login
	 */
	public function authenticate() {
		extract($this->prepare_post(array('username', 'password')));
		$password 	= crypt($password, $this->salt);
		$username	= $this->db->escape_str($username);
		
		
	}
	
	/**
	 * Validasi token
	 */
	public function validate_token($token = '') {
		$token 		= (array) \JWT::decode($token, $this->salt);
		$data 		= array('id', 'jenis', 'tokenid');
		foreach ($data as $v) {
			if ( ! isset($token[$v])) return FALSE;
		}
		extract($token);
	}
	
	/**
	 * User signout
	 */
	public function signout($token) {
		// hapus token
		$token 		= (array) \JWT::decode($token, $this->salt);
		$data 		= array('id', 'jenis', 'tokenid');
		foreach ($data as $v) {
			if ( ! isset($token[$v])) return FALSE;
		}
		extract($token);
	}
	
	/**
	 * User info
	 */
	public function me($token) {
		$r = array();
		extract($token);
		
		return $r;
	}
}