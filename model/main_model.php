<?php
/**
 * Main Model
 */
namespace Model;

class MainModel extends ModelBase {
	public function __construct() {
		parent::__construct();
	}
	
	public function get_cds() {
		$r = array();
		$find = $this->db->query("SELECT * FROM cds");
		if ( ! empty($find)) {
			foreach ($find as $val) {
				$r[] = array(
					'id' => $val->id,
					'titel' => $val->titel,
					'interpret' => $val->interpret,
					'jahr' => $val->jahr
				);
			}
		}
		return $r;
	}
}

