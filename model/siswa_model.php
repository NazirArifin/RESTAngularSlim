<?php
/**
 * Main Model
 */
namespace Model;

class SiswaModel extends ModelBase {
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * dapatkan daftar siswa
	 */
	public function get_list() {
		extract($this->prepare_get(array('cpage', 'nama', 'kelas')));
		$nama 	= $this->db->escape_str($nama);
		$kelas 	= intval($kelas);
		$cpage 	= intval($cpage);
		
		// kondisi
		$where	= array();
		$where[] = "a.KD_KELAS = b.KD_KELAS";
		$where[] = "a.STATUS_SISWA = '1'";
		if ( ! empty($nama)) $where[] = "(a.NAMA_SISWA LIKE '%{$nama}%' OR a.NIS_SISWA LIKE '%{$nama}%' OR a.NISN_SISWA LIKE '%{$nama}%')";
		if ( ! empty($kelas)) $where[] = "a.KD_KELAS = '$kelas'";
		
		$r = array();
		// hitung jumlah halaman
		$numdt = 25;
		$start = $cpage * $numdt;
		$run = $this->db->query("SELECT COUNT(a.ID_SISWA) AS HASIL FROM siswa a, kelas b WHERE " . implode(" AND ", $where), true);
		$numpg = ceil($run->HASIL / $numdt);
		
		// cari data sebenarnya
		$run = $this->db->query("SELECT a.ID_SISWA, a.NIS_SISWA, a.NISN_SISWA, a.NAMA_SISWA, a.TETALA_SISWA, a.TGL_MASUK_SISWA, a.JK_SISWA, b.NAMA_KELAS, a.ALAMAT_SISWA, a.TELP_SISWA, a.HP_SISWA FROM siswa a, kelas b WHERE " . implode(" AND ", $where) . " ORDER BY a.KD_KELAS, a.NAMA_SISWA, a.NIS_SISWA LIMIT $start, $numdt");
		if ( ! empty($run)) {
			foreach ($run as $val) {
				$r[] = array(
					'check'	=> false,
					'id' 	=> $val->ID_SISWA,
					'nama'	=> $val->NAMA_SISWA,
					'nis'	=> $val->NIS_SISWA,
					'nisn'	=> $val->NISN_SISWA,
					'kelas'	=> $val->NAMA_KELAS,
					'alamat'=> $val->ALAMAT_SISWA,
					'telp'	=> $val->TELP_SISWA,
					'hp'	=> $val->HP_SISWA,
					'masuk' => datedb_to_tanggal($val->TGL_MASUK_SISWA, 'd/m/Y'),
					'tetala'=> str_replace(' - ', '/', $val->TETALA_SISWA),
					'jk'	=> ($val->JK_SISWA == 'l' ? 'Laki-laki' : 'Perempuan')
				);
			}
		}
		
		return array('type' => true, 'siswa' => $r, 'numpage' => $numpg);
	}
	
	/**
	 * Dapatkan detail siswa
	 */
	public function get_detail($id, $is_form = true) {
		if ($is_form) {
			$siswa = $ayah = $ibu = $wali = array();
			// siswa
			$run = $this->db->query("SELECT * FROM siswa WHERE ID_SISWA = '$id'", true);
			if (empty($run)) return false;
			list($tempat, $waktu) = explode(', ', $run->TETALA_SISWA);
			list($tanggal, $bulan, $tahun) = explode(' - ', $waktu);
			$siswa = array(
				'id' => $id, 'nama' => $run->NAMA_SISWA, 'kelas' => $run->KD_KELAS, 'nis' => $run->NIS_SISWA, 'nisn' => $run->NISN_SISWA,
				'tempat' => $tempat, 'tanggal' => intval($tanggal), 'bulan' => intval($bulan), 'tahun' => intval($tahun),
				'jk' => $run->JK_SISWA, 'agama' => $run->ID_AGAMA, 'masuk' => datedb_to_tanggal($run->TGL_MASUK_SISWA, 'd/m/Y'),
				'pendidikan' => $run->PNDSBLM_SISWA, 'telepon' => $run->TELP_SISWA, 'handphone' => $run->HP_SISWA, 'email' => $run->EMAIL_SISWA,
				'alamat' => $run->ALAMAT_SISWA, 'rt' => $run->RT_SISWA, 'rw' => $run->RW_SISWA, 'dusun' => $run->DUSUN_SISWA,
				'desa' => $run->DESA_SISWA, 'kecamatan' => $run->KECAMATAN_SISWA, 'kabupaten' => $run->KABUPATEN_SISWA, 
				'provinsi' => $run->PROVINSI_SISWA, 'jenistinggal' => $run->ID_JENIS_TINGGAL, 'transport' => $run->ID_ALAT_TRANSPORT
			);
			
			// ayah
			$run = $this->db->query("SELECT * FROM ayah WHERE ID_SISWA = '$id'", true);
			if (empty($run)) return false;
			$ayah = array(
				'id' => $run->ID_AYAH, 'nama' => $run->NAMA_AYAH, 'tahun' => $run->THNLHR_AYAH, 'pendidikan' => $run->ID_PENDIDIKAN, 
				'pekerjaan' => $run->PEKERJAAN_AYAH, 'penghasilan' => number_format($run->PENGHASILAN_AYAH, 0, ',', '.')
			);
			// ibu
			$run = $this->db->query("SELECT * FROM ibu WHERE ID_SISWA = '$id'", true);
			if (empty($run)) return false;
			$ibu = array(
				'id' => $run->ID_IBU, 'nama' => $run->NAMA_IBU, 'tahun' => $run->THNLHR_IBU, 'pendidikan' => $run->ID_PENDIDIKAN, 
				'pekerjaan' => $run->PEKERJAAN_IBU, 'penghasilan' => number_format($run->PENGHASILAN_IBU, 0, ',', '.')
			);
			// wali
			$wali = array(
				'id' => 0, 'nama' => '', 'tahun' => date('Y') - 25, 'pendidikan' => '1', 'pekerjaan' => '', 'penghasilan' => '', 'alamat' => ''
			);
			$run = $this->db->query("SELECT * FROM wali WHERE ID_SISWA = '$id'", true);
			if ( ! empty($run)) {
				$wali['id'] 	= $run->ID_WALI;
				$wali['nama'] 	= $run->NAMA_WALI;
				$wali['tahun'] 	= $run->THNLHR_WALI;
				$wali['pendidikan'] = $run->ID_PENDIDIKAN;
				$wali['pekerjaan'] = $run->PEKERJAAN_WALI;
				$wali['penghasilan'] = number_format($run->PENGHASILAN_WALI, 0, ',', '.');
				$wali['alamat'] = $run->ALAMAT_WALI;
			}
			return array(
				'siswa' => $siswa, 'ayah' => $ayah, 'ibu' => $ibu, 'wali' => $wali
			);
		} else {
			$siswa = $ayah = $ibu = $wali = array();
			$where = array();
			$where[] = "a.ID_AGAMA = b.ID_AGAMA";
			$where[] = "a.KD_KELAS = c.KD_KELAS";
			$where[] = "a.ID_JENIS_TINGGAL = d.ID_JENIS_TINGGAL";
			$where[] = "a.ID_ALAT_TRANSPORT = e.ID_ALAT_TRANSPORT";
			$where[] = "a.ID_SISWA = '$id'";
			$run	= $this->db->query("SELECT a.*, b.NAMA_AGAMA, c.NAMA_KELAS, d.NAMA_JENIS_TINGGAL, e.NAMA_ALAT_TRANSPORT FROM siswa a, agama b, kelas c, jenis_tinggal d, alat_transport e WHERE " . implode(" AND ", $where), true);
			list($tempat, $waktu) = explode(', ', $run->TETALA_SISWA);
			list($date, $month, $year) = explode(' - ', $waktu);
			
			$siswa = array(
				'nis'	=> $run->NIS_SISWA,
				'nisn'	=> $run->NISN_SISWA,
				'nama'	=> $run->NAMA_SISWA,
				'jk'	=> ($run->JK_SISWA == 'l' ? 'Laki-laki' : 'Perempun'),
				'tetala'=> $tempat . ', ' . datedb_to_tanggal("$year-$month-$date", 'd F Y'),
				'masuk'	=> datedb_to_tanggal($run->TGL_MASUK_SISWA, 'd F Y'),
				'pendidikan'=> ($run->PNDSBLM_SISWA == '1' ? 'TK' : 'Home Schooling'),
				'alamat'=> $run->ALAMAT_SISWA,
				'rt'	=> $run->RT_SISWA,
				'rw'	=> $run->RW_SISWA,
				'dusun'	=> $run->DUSUN_SISWA,
				'desa'	=> $run->DESA_SISWA,
				'kecamatan'=> $run->KECAMATAN_SISWA,
				'kabupaten'=> $run->KABUPATEN_SISWA,
				'provinsi'=> $run->PROVINSI_SISWA,
				'kdpos'	=> (empty($run->KDPOS_SISWA) ? '' : $run->KDPOS_SISWA),
				'telepon'=> $run->TELP_SISWA,
				'hp'	=> $run->HP_SISWA,
				'email'	=> $run->EMAIL_SISWA,
				'agama'	=> $run->NAMA_AGAMA,
				'kelas'	=> $run->NAMA_KELAS,
				'jenistinggal' => $run->NAMA_JENIS_TINGGAL,
				'transport'=> $run->NAMA_ALAT_TRANSPORT
			);
			// cari data ayah
			$run = $this->db->query("SELECT a.*, b.NAMA_PENDIDIKAN FROM ayah a, pendidikan b WHERE a.ID_PENDIDIKAN = b.ID_PENDIDIKAN AND a.ID_SISWA = '$id'", true);
			$ayah = array(
				'nama'	=> $run->NAMA_AYAH,
				'lahir'	=> $run->THNLHR_AYAH,
				'pekerjaan' => $run->PEKERJAAN_AYAH,
				'pendidikan' => $run->NAMA_PENDIDIKAN,
				'penghasilan' => number_format($run->PENGHASILAN_AYAH, 0, ',', '.')
			);
			// cari data ayah
			$run = $this->db->query("SELECT a.*, b.NAMA_PENDIDIKAN FROM ibu a, pendidikan b WHERE a.ID_PENDIDIKAN = b.ID_PENDIDIKAN AND a.ID_SISWA = '$id'", true);
			$ibu = array(
				'nama'	=> $run->NAMA_IBU,
				'lahir'	=> $run->THNLHR_IBU,
				'pekerjaan' => $run->PEKERJAAN_IBU,
				'pendidikan' => $run->NAMA_PENDIDIKAN,
				'penghasilan' => number_format($run->PENGHASILAN_IBU, 0, ',', '.')
			);
			// cari data ayah
			$run = $this->db->query("SELECT a.*, b.NAMA_PENDIDIKAN FROM wali a, pendidikan b WHERE a.ID_PENDIDIKAN = b.ID_PENDIDIKAN AND a.ID_SISWA = '$id'", true);
			if ( ! empty($run)) {
				$wali = array(
					'nama'	=> $run->NAMA_WALI,
					'lahir'	=> $run->THNLHR_WALI,
					'pekerjaan' => $run->PEKERJAAN_WALI,
					'pendidikan' => $run->NAMA_PENDIDIKAN,
					'penghasilan' => number_format($run->PENGHASILAN_WALI, 0, ',', '.'),
					'alamat' => $run->ALAMAT_WALI
				);
			}
			return array(
				'type' => true, 
				'siswa' => array(
					'siswa' => $siswa, 'ayah' => $ayah, 'ibu' => $ibu, 'wali' => $wali
				)
			);
		}
	}
	
	/**
	 * simpan data siswa
	 */
	public function save($id = 0) {
		$field 	= array('nama', 'kelas', 'nis', 'nisn', 'tempat', 'tanggal', 'bulan', 'tahun', 'jk', 'agama', 'masuk', 'pendidikan', 'telepon', 'handphone', 'email', 'alamat', 'rt', 'rw', 'dusun', 'desa', 'kecamatan', 'kdpos', 'kabupaten', 'provinsi', 'jenistinggal', 'transport', 
		'ayah_id', 'ayah_nama', 'ayah_tahun', 'ayah_pendidikan', 'ayah_pekerjaan', 'ayah_penghasilan', 
		'ibu_id', 'ibu_nama', 'ibu_tahun', 'ibu_pendidikan', 'ibu_pekerjaan', 'ibu_penghasilan', 
		'wali_id', 'wali_nama', 'wali_alamat', 'wali_tahun', 'wali_pendidikan', 'wali_pekerjaan', 'wali_penghasilan');
		extract($this->prepare_post($field));
		
		// validasi apaan ini
		$fields = array('nama', 'tempat', 'masuk', 'email', 'alamat', 'dusun', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'ayah_nama', 'ayah_pekerjaan',  'ibu_nama', 'ibu_pekerjaan', 'wali_nama', 'wali_alamat', 'wali_pekerjaan');
		foreach ($fields as $val) $$val = $this->escape_str($$val);
		$fieldi = array('kelas', 'tanggal', 'bulan', 'tahun', 'agama', 'pendidikan', 'jenistinggal', 'transport', 
		'ayah_id', 'ayah_tahun', 'ayah_pendidikan', 'ibu_id', 'ibu_tahun', 'ibu_pendidikan', 'wali_id', 'wali_tahun', 'wali_pendidikan');
		foreach ($fieldi as $val) $$val = intval($$val);
		$fieldf = array('nis', 'nisn', 'rt', 'rw', 'kdpos', 'ayah_penghasilan', 'ibu_penghasilan', 'wali_penghasilan');
		foreach ($fieldf as $val) $$val = preg_replace('/[^0-9]/', '', $$val);
		
		$bulan 	+= 1;
		$tanggal= str_pad($tanggal, 2, '0', STR_PAD_LEFT);
		$bulan	= str_pad($bulan, 2, '0', STR_PAD_LEFT);
		$nama	= strtoupper($nama);
		list($d, $m, $y) = explode('/', $masuk);
		$masuk 	= "$y-$m-$d";
		$tetala = ucwords($tempat) . ", $tanggal - $bulan - $tahun";
		$dusun	= ucwords($dusun);
		$desa	= ucwords($desa);
		$kecamatan = ucwords($kecamatan);
		$kabupaten = ucwords($kabupaten);
		$provinsi = ucwords($provinsi);
		
		if (empty($id)) {
			// cari apakah nis sudah ada
			$run = $this->db->query("SELECT COUNT(ID_SISWA) AS HASIL FROM siswa WHERE NIS_SISWA = '$nis'", true);
			if ($run->HASIL > 0) {
				return array( 'type' => false, 'pesan' => 'NIS sudah permah dimasukkan!' );
			}
			
			// insert siswa
			$ins = $this->db->query("INSERT INTO siswa VALUES(0, '$agama', '$kelas', '$jenistinggal', '$transport', '$nis', '$nisn', '$nama', '$jk', '$tetala', '$masuk', '$pendidikan', '$alamat', '$rt', '$rw', '$dusun', '$desa', '$kecamatan', '$kabupaten', '$provinsi', '$kdpos', '$telepon', '$handphone', '$email', '0', '', '1')");
			$idsiswa = $this->db->get_insert_id();
			// insert ke ayah
			$ins = $this->db->query("INSERT INTO ayah VALUES(0, '$idsiswa', '$ayah_pendidikan', '$ayah_nama', '$ayah_tahun', '$ayah_pekerjaan', '$ayah_penghasilan')");
			// insert ke ayah
			$ins = $this->db->query("INSERT INTO ibu VALUES(0, '$idsiswa', '$ibu_pendidikan', '$ibu_nama', '$ibu_tahun', '$ibu_pekerjaan', '$ibu_penghasilan')");
			// insert ke wali jika ada
			if ( ! empty($wali_nama)) {
				$ins = $this->db->query("INSERT INTO wali VALUES(0, '$idsiswa', '$wali_pendidikan', '$wali_nama', '$wali_tahun', '$wali_pekerjaan', '$wali_penghasilan', 'wali_alamat')");
			}
			return array(
				'type' => true, 'pesan' => 'Data siswa berhasil disimpan'
			);
		} else {
			// cari siswa
			$run = $this->db->query("SELECT * FROM siswa WHERE ID_SISWA = '$id'", true);
			if (empty($run)) {
				return array( 'type' => false, 'pesan' => 'Invalid Request!' );
			}
			$upd = array();
			if ($run->NAMA_SISWA != $nama) $upd[] = "NAMA_SISWA = '$nama'";
			if ($run->KD_KELAS != $kelas) $upd[] = "KD_KELAS = '$kelas'";
			if ($run->NIS_SISWA != $nis) $upd[] = "NIS_SISWA = '$nis'";
			if ($run->NISN_SISWA != $nisn) $upd[] = "NISN_SISWA = '$nisn'";
			if ($run->TETALA_SISWA != $tetala) $upd[] = "TETALA_SISWA = '$tetala'";
			if ($run->JK_SISWA != $jk) $upd[] = "JK_SISWA = '$jk'";
			if ($run->ID_AGAMA != $agama) $upd[] = "ID_AGAMA = '$agama'";
			if ($run->TGL_MASUK_SISWA != $masuk) $upd[] = "TGL_MASUK_SISWA = '$masuk'";
			if ($run->PNDSBLM_SISWA != $pendidikan) $upd[] = "PNDSBLM_SISWA = '$pendidikan'";
			if ($run->TELP_SISWA != $telepon) $upd[] = "TELP_SISWA = '$telepon'";
			if ($run->HP_SISWA != $handphone) $upd[] = "HP_SISWA = '$handphone'";
			if ($run->EMAIL_SISWA != $email) $upd[] = "EMAIL_SISWA = '$email'";
			if ($run->ALAMAT_SISWA != $alamat) $upd[] = "ALAMAT_SISWA = '$alamat'";
			if ($run->RT_SISWA != $rt) $upd[] = "RT_SISWA = '$rt'";
			if ($run->RW_SISWA != $rw) $upd[] = "RW_SISWA = '$rw'";
			if ($run->DUSUN_SISWA != $dusun) $upd[] = "DUSUN_SISWA = '$dusun'";
			if ($run->DESA_SISWA != $desa) $upd[] = "DESA_SISWA = '$desa'";
			if ($run->KECAMATAN_SISWA != $kecamatan) $upd[] = "KECAMATAN_SISWA = '$kecamatan'";
			if ($run->KABUPATEN_SISWA != $kabupaten) $upd[] = "KABUPATEN_SISWA = '$kabupaten'";
			if ($run->PROVINSI_SISWA != $provinsi) $upd[] = "PROVINSI_SISWA = '$provinsi'";
			if ($run->ID_JENIS_TINGGAL != $jenistinggal) $upd[] = "ID_JENIS_TINGGAL = '$jenistinggal'";
			if ($run->ID_ALAT_TRANSPORT != $transport) $upd[] = "ID_ALAT_TRANSPORT = '$transport'";
			if ( ! empty($upd)) $update = $this->db->query("UPDATE siswa SET " . implode(", ", $upd) . " WHERE ID_SISWA = '$id'");
			
			$upd = array();
			$run = $this->db->query("SELECT * FROM ayah WHERE ID_SISWA = '$id'", true);
			if ($run->ID_PENDIDIKAN != $ayah_pendidikan) $upd[] = "ID_PENDIDIKAN = '$ayah_pendidikan'";
			if ($run->NAMA_AYAH != $ayah_nama) $upd[] = "NAMA_AYAH = '$ayah_nama'";
			if ($run->PEKERJAAN_AYAH != $ayah_pekerjaan) $upd[] = "PEKERJAAN_AYAH = '$ayah_pekerjaan'";
			if ($run->PENGHASILAN_AYAH != $ayah_penghasilan) $upd[] = "PENGHASILAN_AYAH = '$ayah_penghasilan'";
			if ($run->THNLHR_AYAH != $ayah_tahun) $upd[] = "THNLHR_AYAH = '$ayah_tahun'";
			if ( ! empty($upd)) $update = $this->db->query("UPDATE ayah SET " . implode(", ", $upd) . " WHERE ID_SISWA = '$id'");
			
			$upd = array();
			$run = $this->db->query("SELECT * FROM ibu WHERE ID_SISWA = '$id'", true);
			if ($run->ID_PENDIDIKAN != $ibu_pendidikan) $upd[] = "ID_PENDIDIKAN = '$ibu_pendidikan'";
			if ($run->NAMA_IBU != $ibu_nama) $upd[] = "NAMA_IBU = '$ibu_nama'";
			if ($run->PEKERJAAN_IBU != $ibu_pekerjaan) $upd[] = "PEKERJAAN_IBU = '$ibu_pekerjaan'";
			if ($run->PENGHASILAN_IBU != $ibu_penghasilan) $upd[] = "PENGHASILAN_IBU = '$ibu_penghasilan'";
			if ($run->THNLHR_IBU != $ibu_tahun) $upd[] = "THNLHR_IBU = '$ibu_tahun'";
			if ( ! empty($upd)) $update = $this->db->query("UPDATE ibu SET " . implode(", ", $upd) . " WHERE ID_SISWA = '$id'");
			
			if ( ! empty($wali_nama)) {
				$run = $this->db->query("SELECT * FROM wali WHERE ID_SISWA = '$id'", true);
				if (empty($run)) {
					$ins = $this->db->query("INSERT INTO wali VALUES(0, '$idsiswa', '$wali_pendidikan', '$wali_nama', '$wali_tahun', '$wali_pekerjaan', '$wali_penghasilan', 'wali_alamat')");
				} else {
					$upd = array();
					if ($run->ID_PENDIDIKAN != $wali_pendidikan) $upd[] = "ID_PENDIDIKAN = '$wali_pendidikan'";
					if ($run->NAMA_WALI != $wali_nama) $upd[] = "NAMA_WALI = '$wali_nama'";
					if ($run->PEKERJAAN_WALI != $wali_pekerjaan) $upd[] = "PEKERJAAN_WALI = '$wali_pekerjaan'";
					if ($run->PENGHASILAN_WALI != $wali_penghasilan) $upd[] = "PENGHASILAN_WALI = '$wali_penghasilan'";
					if ($run->THNLHR_WALI != $wali_tahun) $upd[] = "THNLHR_WALI = '$wali_tahun'";
					if ($run->ALAMAT_WALI != $wali_alamat) $upd[] = "ALAMAT_WALI = '$wali_alamat'";
					if ( ! empty($upd)) $update = $this->db->query("UPDATE wali SET " . implode(", ", $upd) . " WHERE ID_SISWA = '$id'");
				}
			}
			
			return array(
				'type' => true, 'pesan' => 'Data siswa berhasil disimpan'
			);
		}
	}
	
	/**
	 * escape string
	 */
	private function escape_str($str) {
		return $this->db->escape_str($str);
	}
}