<?php
/**
 * Driver mysql
 */
namespace Lib\DbDriver;
 
class DbMysql {
	protected $conn_id,
			$queries = array(),
			$result_id,
			$query_count = 0,
			$affected_rows = 0,
			$error = '';
	
	public function __construct($params) {
		if (is_array($params)) {
			foreach ($params as $key => $val) {
				$this->$key = $val;
			}
		}
		$this->initialize();
	}
	
	public function initialize() {
		if (is_resource($this->conn_id) OR is_object($this->conn_id))
			return TRUE;
		
		$this->conn_id = $this->db_connect();
		
		if (FALSE === $this->conn_id) {
			$e = $this->connect_error();
			exit('Database connection failed with message: "' . $e[1] . '"');
			return FALSE;
		}
		
		// select db
		if (FALSE === $this->db_select()) {
			exit('Can\'t select database');
			return FALSE;
		}
		
		return TRUE;
	}
	
	public function version() {
		if ($this->_version() === FALSE) {
			return FALSE;
		}
		
		$sql = $this->_version();
		$query = $this->query($sql, TRUE);
		return $query->ver;
	}
	
	public function query($sql, $first = FALSE, $return_object = TRUE) {
		if ($sql == '') return FALSE;
		
		// Simpan query
		$this->queries[] = $sql;
		
		// Run Query
		$this->result_id = $this->simple_query($sql);
		
		if (FALSE === $this->result_id) {
			$msg = 'Database query error in: ' . $this->error_number() . ', with message: ' . $this->error_message() . ', for query: ' . $sql;
			$this->error = $msg;
			return FALSE;
		}
		
		$this->query_count++;
		
		if (preg_match('/^\\s*(insert|delete|update|replace|alter) /i', $sql)) {
			$this->affected_rows = $this->get_affected_rows();
			if (preg_match('/^\\s*(insert|replace) /i', $sql)) {
				$this->insert_id = $this->get_insert_id();
			}
			
			$r = $this->affected_rows;
		} else {
			if (is_bool($this->result_id)) return $this->result_id;
			
			$r = array();
			
			if ($return_object === TRUE) {
				$r = $this->fetch_object($r);
			} else {
				$r = $this->fetch_array($r);
			}
			
			$this->free_result();
			
			if ($first === TRUE) {
				$r = $r[0];
			}
		}
		
		return $r;
	}
	
	public function get_error() {
		return $this->error;
	}
	
	protected function simple_query($sql) {
		if ( ! $this->conn_id) {
			$this->initialize();
		}

		return $this->_execute($sql);
	}
	
	public function close() {
		if (is_resource($this->conn_id) OR is_object($this->conn_id)) {
			$this->_close($this->conn_id);
		}
		$this->conn_id = FALSE;
	}
	
	public function total_queries() {
		return $this->query_count;
	}
	
	protected function db_connect() {
		if ($this->port != '') {
			$this->hostname .= ':' . $this->port;
			return @mysql_connect($this->hostname, $this->username, $this->password);
		} else {
			return @mysql_connect($this->hostname, $this->username, $this->password);
		}
	}
	
	public function db_select() {
		return mysql_select_db($this->database);
	}
	
	protected function connect_error() {
		if (is_null($this->conn_id))
			return FALSE;
			
		return array(@mysql_errno(), @mysql_error());
	}
	
	protected function db_set_charset($charset, $collation) {
		return @mysql_query($this->conn_id, "SET NAMES '" . $this->escape_str($charset) . "' COLLATE '" . $this->escape_str($collation) . "'");
	}
	
	public function escape_str($str, $like = FALSE) {
		if (is_array($str)) {
			foreach ($str as $key => $val) {
				$str[$key] = $this->escape_str($val, $like);
			}
			
			return;
		}
		
		if (function_exists('mysql_escape_string')) {
			$str = @mysql_escape_string($str);
		} else {
			$str = addslashes($str);
		}
		
		// Escape LIKE condition
		if ($like) {
			$str = str_replace(array('%', '_'), array('\\%', '\\_'), $str);
		}
		
		return $str;
	}
	
	protected function _version() {
		return "SELECT version() AS ver";
	}
	
	protected function _execute($sql) {
		$result = mysql_query($sql, $this->conn_id);
		return $result;
	}
	
	protected function error_message() {
		return mysql_error($this->conn_id);
	}

	protected function error_number() {
		return mysql_errno($this->conn_id);
	}
	
	public function get_affected_rows() {
		return @mysql_affected_rows($this->conn_id);
	}
	
	public function get_insert_id() {
		return @mysql_insert_id($this->conn_id);
	}
	
	protected function fetch_object() {
		if (empty($this->result_id)) return false;
		$r = array();
		while($row = @mysql_fetch_object($this->result_id)) {
			$r[] = $row;
		}
		return $r;
	}
	
	protected function fetch_array() {
		if (empty($this->result_id)) return false;
		$r = array();
		while($row = @mysql_fetch_array($this->result_id, MYSQL_BOTH)) {
			$r[] = $row;
		}
		return $r;
	}
	
	protected function free_result() {
		if (empty($this->result_id)) return;
		@mysql_free_result($this->result_id);
	}
	
	function _close($conn_id) {
		@mysql_close($conn_id);
	}
}