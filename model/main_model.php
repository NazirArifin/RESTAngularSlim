<?php
/**
 * Main Model
 */
namespace Model;

class MainModel extends ModelBase {
	public function __construct() {
		parent::__construct();
	}
	
	private $salt 	= 'tika08';
	
	/**
	 * User login
	 */
	public function authenticate() {
		extract($this->prepare_post(array('username', 'password')));
		$password 	= crypt($password, $this->salt);
		$username	= $this->db->escape_str($username);
		
		// cari di database
		$user 	= $this->db->query("SELECT ID_USER, JENIS_USER FROM user WHERE USERNAME_USER = '$username' AND PASSWORD_USER = '$password' AND STATUS_USER = '1'", true);
		if (empty($user)) return array('type' => false, 'data' => 'username/password invalid');
		$iduser	= $user->ID_USER;
		$jnuser = $user->JENIS_USER;
		
		// hapus token
		$hapus	= $this->db->query("DELETE FROM token WHERE ID_USER = '$iduser'");
		
		// masukkan token
		$expired 	= time() + (5 * 24 * 3600);
		$insert		= $this->db->query("INSERT INTO token VALUES(0, '$iduser', '', '" . date('Y-m-d H:i:s', $expired) . "')");
		$tokenid	= $this->db->get_insert_id();
		$token	= array(
			'id' 		=> $iduser,
			'jenis' 	=> $jnuser,
			'tokenid'	=> $tokenid
		);
		$token	= \JWT::encode($token, $this->salt);
		$upd	= $this->db->query("UPDATE token SET DATA_TOKEN = '$token' WHERE ID_TOKEN = '$tokenid'");
		return array(
			'type' => true, 'token' => $token, 'expired' => $expired
		);
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
		
		// periksa apakah user masih aktif
		$cari	= $this->db->query("SELECT COUNT(ID_USER) AS HASIL FROM user WHERE ID_USER = '$id' AND STATUS_USER = '1'", true);
		if ($cari->HASIL == 0) {
			$del = $this->db->query("DELETE FROM token WHERE ID_USER = '$id'");
			return false;
		}
		
		// periksa di token apakah ada
		$cari 	= $this->db->query("SELECT COUNT(ID_TOKEN) AS HASIL FROM token WHERE ID_TOKEN = '$tokenid' AND NOW() < EXPIRED_TOKEN", true);
		if ($cari->HASIL == 0) return false;
		
		// perbarui token
		$exp 		= time() + (5 * 24 * 3600);
		$upd 		= $this->db->query("UPDATE token SET EXPIRED_TOKEN = '" . date('Y-m-d H:i:s', $exp) . "' WHERE ID_TOKEN = '$tokenid'");
		return $token;
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
		
		// periksa di database tabel token
		$tokenid 	= floatval($tokenid);
		$del 		= $this->db->query("DELETE FROM token WHERE ID_TOKEN = '$tokenid'");
		return array( 'type' => TRUE );
	}
	
	/**
	 * User info
	 */
	public function me($token) {
		$r = array();
		extract($token);
		
		$r['isadmin'] = $r['isguru'] = $r['iswali'] = $r['iskepsek'] = false;
		switch ($jenis) {
			case '1': // admin
				$r['isadmin'] = true;
				$run = $this->db->query("SELECT ID_ADMIN, NAMA_ADMIN FROM admin WHERE ID_USER = '$id'", true);
				$r['nama'] = $run->NAMA_ADMIN;
				$r['id'] = $run->ID_ADMIN;
				break;
			case '2': // guru
				$r['isguru'] = true;
				
				break;
			case '3': // guru + wali kelas
				$r['isguru'] = $r['iswali'] = true;
				
				break;
			case '4': // guru + kepala sekolah
				$r['isguru'] = $r['iskepsek'] = true;
				
				break;
			case '5': // guru + wali kelas + kepala sekolah
				$r['isguru'] = $r['iswali'] = $r['iskepsek'] = true;
				
				break;
		}
		return $r;
	}
	
	/**
	 * Simpan data guru
	 */
	public function save_guru($id = 0) {
		extract($this->prepare_post(array('kepsek', 'nama', 'jenis', 'nip', 'golongan', 'jk', 'agama', 'alamat', 'telepon', 'username', 'password', 'password2')));
		$kepsek		= (empty($kepsek) ? false : true);
		$nama 		= $this->db->escape_str($nama);
		$nip		= preg_replace('/[^0-9]/', '', $nip);
		$alamat		= $this->db->escape_str($alamat);
		$telepon	= $this->db->escape_str($telepon);
		$username	= $this->db->escape_str($username);
		$tipeuser	= ($kepsek ? 4 : 2);
		$golongan	= (empty($golongan) ? 0 : $golongan);
		$jabatan	= ($kepsek ? 'kepsek' : 'guru');
		
		if (empty($id)) {
			// validasi
			if ($password != $password2)
				return array('type' => false, 'pesan' => 'Password tidak identik!');
			if (strlen($username) < 6) 
				return array('type' => false, 'pesan' => 'Username invalid!');
				
			// simpan ke database
			$pass	= crypt($password, $this->salt);
			$insert	= $this->db->query("INSERT INTO user VALUES(0, '$username', '$pass', '$tipeuser', '1')");
			$iduser	= $this->db->get_insert_id();
			
			$insert	= $this->db->query("INSERT INTO guru VALUES(0, '$agama', '$golongan', '0', '$iduser', '$nip', '$nama', '$jk', '$alamat', '$telepon', '$jabatan', '$jenis')");
			return array('type' => true, 'pesan' => 'Data guru berhasil tersimpan');
		} else {
			$cari	= $this->db->query("SELECT * FROM guru WHERE ID_GURU = '$id'", true);
			if (empty($cari)) return array('type' => false, 'pesan' => 'Request invalid!');
			
			$upd = array();
			if ($cari->KD_GOLONGAN != $golongan) $upd[] = "KD_GOLONGAN = '$golongan'";
			if ($cari->NIP_GURU != $nip) $upd[] = "NIP_GURU = '$nip'";
			if ($cari->NAMA_GURU != $nama) $upd[] = "NAMA_GURU = '$nama'";
			if ($cari->JK_GURU != $jk) $upd[] = "JK_GURU = '$jk'";
			if ($cari->ALAMAT_GURU != $alamat) $upd[] = "ALAMAT_GURU = '$alamat'";
			if ($cari->TELP_GURU != $telepon) $upd[] = "TELP_GURU = '$telepon'";
			if ($cari->JABATAN_GURU != $jabatan) $upd[] = "JABATAN_GURU = '$jabatan'";
			if ($cari->JENIS_GURU != $jenis) $upd[] = "JENIS_GURU = '$jenis'";
			
			if ( ! empty($upd)) {
				$run = $this->db->query("UPDATE guru SET " . implode(", ", $upd) . " WHERE ID_GURU = '$id'");
			}
			return array('type' => true, 'pesan' => 'Data guru berhasil diubah');
		}
	}
	
	/**
	 * Dapatkan data sekolah
	 */
	public function get_school_data() {
		$r 		= array();
		// data sekolah
		$run 	= $this->db->query("SELECT * FROM sekolah", true);
		if ( ! empty($run)) {
			$f		= array('nomor', 'nama', 'alamat', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'kodepos', 'website', 'email', 'telepon');
			foreach ($f as $val) {
				$field = strtoupper($val) . '_SEKOLAH';
				$r[$val] = $run->$field;
			}
		}
		// akademik
		$run	= $this->db->query("SELECT * FROM akademik WHERE STATUS_AKADEMIK = '1'", true);
		if ( ! empty($run)) {
			$r['akademik'] = array(
				'id' 	=> $run->ID_AKADEMIK,
				'tahun'	=> $run->THN_AKADEMIK,
				'semester' => $run->SEMESTER_AKADEMIK
			);
		}
		// kepala sekolah
		$run 	= $this->db->query("SELECT ID_GURU, NAMA_GURU, NIP_GURU FROM guru WHERE JABATAN_GURU = 'kepsek'", true);
		if ( ! empty($run)) {
			$r['kepsek'] = array(
				'id'	=> $run->ID_GURU,
				'nama'	=> $run->NAMA_GURU,
				'nip'	=> $run->NIP_GURU
			);
		}
		
		return $r;
	}
	
	/**
	 * Mendapatkan data golongan
	 */
	public function get_golongan_data() {
		$r = array();
		$run = $this->db->query("SELECT * FROM golongan ORDER BY NAMA_GOLONGAN");
		if ( ! empty($run)) {
			foreach ($run as $val) {
				$r[] = array(
					'id' 	=> $val->KD_GOLONGAN,
					'nama' 	=> $val->NAMA_GOLONGAN,
					'gaji'	=> $val->BESARAN_GAJI
				);
			}
		}
		return $r;
	}
	
	/**
	 * Mendapatkan data agama
	 */
	public function get_agama_data() {
		$r = array();
		$run = $this->db->query("SELECT * FROM agama");
		if ( ! empty($run)) {
			foreach ($run as $val) {
				$r[] = array(
					'id' 	=> $val->ID_AGAMA,
					'nama'	=> $val->NAMA_AGAMA
				);
			}
		}
		return $r;
	}
	
	/**
	 * Mendapatkan data kelas
	 */
	public function get_kelas_data() {
		$r = array();
		$run = $this->db->query("SELECT * FROM kelas ORDER BY NAMA_KELAS");
		if ( ! empty($run)) {
			foreach ($run as $val) {
				$r[] = array(
					'id'	=> $val->KD_KELAS,
					'nama'	=> $val->NAMA_KELAS,
					'info'	=> ( ! is_null($val->KETERANGAN_KELAS) ?  $val->KETERANGAN_KELAS : '')
				);
			}
		}
		return $r;
	}
	
	/**
	 * Mendapatkan jenis tinggal
	 */
	public function get_jenis_tinggal_data() {
		$r = array();
		$run = $this->db->query("SELECT * FROM jenis_tinggal");
		if ( ! empty($run)) {
			foreach ($run as $val) {
				$r[] = array(
					'id' 	=> $val->ID_JENIS_TINGGAL,
					'nama'	=> $val->NAMA_JENIS_TINGGAL
				);
			}
		}
		return $r;
	}
	
	/**
	 * Mendapatkan alat transport
	 */
	public function get_alat_transport_data() {
		$r = array();
		$run = $this->db->query("SELECT * FROM alat_transport");
		if ( ! empty($run)) {
			foreach ($run as $val) {
				$r[] = array(
					'id' 	=> $val->ID_ALAT_TRANSPORT,
					'nama'	=> $val->NAMA_ALAT_TRANSPORT
				);
			}
		}
		return $r;
	}
	
	/**
	 * Mendapatkan alat transport
	 */
	public function get_pendidikan_data() {
		$r = array();
		$run = $this->db->query("SELECT * FROM pendidikan");
		if ( ! empty($run)) {
			foreach ($run as $val) {
				$r[] = array(
					'id' 	=> $val->ID_PENDIDIKAN,
					'nama'	=> $val->NAMA_PENDIDIKAN
				);
			}
		}
		return $r;
	}
	
	
	/**
	 * dapatkan suggest
	 */
	public function get_suggest($f = '') {
		$r = array();
		extract($this->prepare_get(array('q')));
		$q = $this->db->escape_str($q);
		switch ($f) {
			case 'tempat':
				$cari = $this->db->query("SELECT TETALA_SISWA FROM siswa WHERE TETALA_SISWA LIKE '{$q}%'");
				if ( ! empty($cari)) {
					foreach ($cari as $val) {
						list($tempat, $waktu) = explode(', ', $val->TETALA_SISWA);
						if ( ! in_array($tempat, $r)) $r[] = $tempat;
					}
				}
				break;
			case 'desa': case 'kecamatan': case 'kabupaten': case 'provinsi':
				$kolom = strtoupper($f) . '_SISWA';
				$cari = $this->db->query("SELECT $kolom FROM siswa WHERE $kolom LIKE '{$q}%' GROUP BY $kolom");
				if ( ! empty($cari)) {
					foreach ($cari as $val) $r[] = $val->$kolom;
				}
				break;
			case 'kerjaayah': case 'kerjaibu': case 'kerjawali':
				$tabel = str_replace('kerja', '', $f);
				$upper = strtoupper($f);
				$kolom = str_replace('KERJA', 'PEKERJAAN_', $upper);
				$cari = $this->db->query("SELECT $kolom FROM $tabel WHERE $kolom LIKE '{$q}%' GROUP BY $kolom");
				if ( ! empty($cari)) {
					foreach ($cari as $val) $r[] = $val->$kolom;
				}
				break;
		}
		return $r;
	}
}