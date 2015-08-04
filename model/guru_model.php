<?php
/**
 * Main Model
 */
namespace Model;

class GuruModel extends ModelBase {
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Dapatkan data list guru
	 */
	public function get_list() {
		$r = array();
		extract($this->prepare_get(array('cpage')));
		$numpg 	= 0;
		$numdt 	= 20;
		$cpage 	= intval($cpage);
		// jumlah halaman
		$run 	= $this->db->query("SELECT COUNT(a.ID_GURU) AS HASIL FROM guru a, user b WHERE a.ID_USER = b.ID_USER AND b.STATUS_USER = '1'", true);
		$numpg 	= ceil($run->HASIL / $numdt);
		$start 	= $cpage * $numdt;
		// data sebenarnya
		$run	= $this->db->query("SELECT a.* FROM guru a, user b WHERE a.ID_USER = b.ID_USER AND b.STATUS_USER = '1' ORDER BY a.JENIS_GURU, a.NIP_GURU, a.NAMA_GURU LIMIT $start, $numdt");
		if ( ! empty($run)) {
			foreach ($run as $val) {
				// cari golongan
				if ($val->JENIS_GURU == '1') {
					$srun = $this->db->query("SELECT NAMA_GOLONGAN FROM golongan WHERE KD_GOLONGAN = '" . $val->KD_GOLONGAN . "'", true);
					$golongan = $srun->NAMA_GOLONGAN;
				} else $golongan = '';
				
				$r[] = array(
					'id' 	=> $val->ID_GURU,
					'nama'	=> ucwords($val->NAMA_GURU),
					'nip'	=> $val->NIP_GURU,
					'alamat'=> $val->ALAMAT_GURU,
					'telepon'=> $val->TELP_GURU,
					'jabatan'=> ($val->JABATAN_GURU == 'kepsek' ? 'Kepala Sekolah' : 'Guru'),
					'jk'	=> ($val->JK_GURU == 'l' ? 'Laki-laki' : 'Perempuan'),
					'jenis'	=> ($val->JENIS_GURU == '1' ? 'PNS' : 'Bantu/Honorer'),
					'golongan'=> $golongan
				);
			}
		}
		return array('type' => true, 'guru' => $r, 'numpage' => $numpg);
	}
	
	/**
	 * Detail guru
	 */
	public function get_detail($id, $isform = true) {
		$cari = $this->db->query("SELECT * FROM guru WHERE ID_GURU = '$id'", true);
		if ($isform) {
			if (empty($cari)) return array();
			return array(
				'id'	=> $cari->ID_GURU,
				'kepsek' => ($cari->JABATAN_GURU == 'kepsek'),
				'nama'	=> $cari->NAMA_GURU,
				'jenis' => intval($cari->JENIS_GURU),
				'nip'	=> $cari->NIP_GURU,
				'golongan' => $cari->KD_GOLONGAN,
				'jk'	=> $cari->JK_GURU,
				'agama'	=> $cari->ID_AGAMA,
				'alamat' => $cari->ALAMAT_GURU,
				'telepon' => $cari->TELP_GURU
			);
		}
	}
}