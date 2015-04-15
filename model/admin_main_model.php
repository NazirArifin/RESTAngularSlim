<?php
/**
 * Main Model
 */
namespace Model;

class AdminMainModel extends ModelBase {
	public function __construct() {
		parent::__construct();
	}
	
	public function cds() {
		$run = $this->db->query("SELECT * FROM cds");
		if ( ! empty($run)) {
			foreach ($run as $val) {
				$r[] = array(
					'titel'		=> $val->titel,
					'interpret'	=> $val->interpret,
					'jahr' 		=> $val->jahr,
					'id' 		=> $val->id
				);
			}
		}
		return $r;
	}
}

