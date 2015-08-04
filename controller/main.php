<?php
$app->options('/', function() use($app) { $app->status(200); $app->stop(); });
$app->get('/', function() use($app, $ctr) {
	if (is_array(cek_token($ctr, $app))) redirect_home($app);
	$ctr->load('model', 'main');
	$ctr->load('view', 'index.html', array(
		'page' => 'index',
		'sekolah' => $ctr->MainModel->get_school_data()
	));
});

$app->options('/login', function() use($app) { $app->status(200); $app->stop(); });
$app->post('/login', function() use($app, $ctr) {
	$ctr->load('file', 'lib/JWT.php');
	$ctr->load('model', 'main');
	$r = $ctr->MainModel->authenticate();
	if ($r['type']) {
		save_token($r['token'], $r['expired']);
		redirect_home($app);
	} else redirect_index($app, $r['data']);
});

$app->options('/logout', function() use($app) { $app->status(200); $app->stop(); });
$app->get('/logout', function() use($app, $ctr) {
	$ctr->load('file', 'lib/JWT.php');
	$ctr->load('model', 'main');
	$ctr->MainModel->signout($_COOKIE['token']);
	delete_token();
	redirect_index($app);
});

$app->options('/home', function() use($app) { $app->status(200); $app->stop(); });
$app->get('/home', function() use($app, $ctr) {
	// cek token dan load data
	$token 	= cek_token($ctr, $app); if ($token === false) redirect_index($app);
	$data 	= $ctr->MainModel->me($token);
	$data['sekolah'] = $ctr->MainModel->get_school_data();
	
	$ctr->load('view', 'home.html', $data);
});

// ------------------------------ GURU SECTION -------------------------------------
$app->options('/guru', function() use($app) { $app->status(200); $app->stop(); });
$app->get('/guru', function() use($app, $ctr) {
	// cek token dan load data
	$token 	= cek_token($ctr, $app); if ($token === false) redirect_index($app);
	$data 	= $ctr->MainModel->me($token);
	$data['sekolah'] = $ctr->MainModel->get_school_data();
	if (isset($_GET['type'])) {
		$data['notifikasi'] = array(
			'type' => $_GET['type'], 'pesan' => ( ! isset($_GET['pesan']) ? '' : $_GET['pesan'])
		);
	}
	$ctr->load('view', 'guru.html', $data);
});

/**
 * Group: guru
 */
$app->group('/guru', function() use ($app, $ctr) {
	$app->options('/tambah', function() use($app) { $app->status(200); $app->stop(); });
	$app->get('/tambah', function() use($app, $ctr) {
		// cek token dan load data
		$token 	= cek_token($ctr, $app); if ($token === false) redirect_index($app);
		$data 	= $ctr->MainModel->me($token);
		$data['sekolah'] = $ctr->MainModel->get_school_data();
		$data['golongan'] = $ctr->MainModel->get_golongan_data();
		$data['mode'] = 'Tambah';
		$ctr->load('view', 'guru-tambah.html', $data);
	});

	$app->post('/tambah', function() use($app, $ctr) {
		// cek token dan load data
		$token 	= cek_token($ctr, $app); if ($token === false) redirect_index($app);
		$r = $ctr->MainModel->save_guru();
		$url = ($r['type'] ? '/guru?' : '/guru/tambah?');
		$app->redirect($url . http_build_query($r));
	});
	
	$app->options('/:id', function() use($app) { $app->status(200); $app->stop(); });
	$app->get('/:id', function($id) use($app, $ctr) {
		// cek token dan load data
		$token 	= cek_token($ctr, $app); if ($token === false) redirect_index($app);
		$data 	= $ctr->MainModel->me($token);
		$data['sekolah'] = $ctr->MainModel->get_school_data();
		$data['golongan'] = $ctr->MainModel->get_golongan_data();
		$data['mode'] = 'Edit';
		$ctr->load('model', 'guru');
		$data['guru'] = $ctr->GuruModel->get_detail($id);
		
		$ctr->load('view', 'guru-tambah.html', $data);
	})->conditions(array('id' => '[0-9]+'));
	
	$app->post('/:id', function($id) use($app, $ctr) {
		// cek token dan load data
		$token 	= cek_token($ctr, $app); if ($token === false) redirect_index($app);
		$r = $ctr->MainModel->save_guru($id);
		$app->redirect('/guru?' . http_build_query($r));
	})->conditions(array('id' => '[0-9]+'));
});

// ------------------------------ SISWA SECTION -------------------------------------
$app->options('/siswa', function() use($app) { $app->status(200); $app->stop(); });
$app->get('/siswa', function() use($app, $ctr) {
	// cek token dan load data
	$token 	= cek_token($ctr, $app); if ($token === false) redirect_index($app);
	$data 	= $ctr->MainModel->me($token);
	$data['sekolah'] = $ctr->MainModel->get_school_data();
	if (isset($_GET['type'])) {
		$data['notifikasi'] = array(
			'type' => $_GET['type'], 'pesan' => ( ! isset($_GET['pesan']) ? '' : $_GET['pesan'])
		);
	}
	$data['kelas'] = $ctr->MainModel->get_kelas_data();
	$data['page'] = 'siswa';
	$ctr->load('view', 'siswa.html', $data);
});

/**
 * Group: siswa
 */
$app->group('/siswa', function() use ($app, $ctr) {
	$app->options('/tambah', function() use($app) { $app->status(200); $app->stop(); });
	$app->get('/tambah', function() use($app, $ctr) {
		// cek token dan load data
		$token 	= cek_token($ctr, $app); if ($token === false) redirect_index($app);
		$data 	= $ctr->MainModel->me($token);
		$data['sekolah'] = $ctr->MainModel->get_school_data();
		$data['agama'] = $ctr->MainModel->get_agama_data();
		$data['jenistinggal'] = $ctr->MainModel->get_jenis_tinggal_data();
		$data['transport'] = $ctr->MainModel->get_alat_transport_data();
		$data['pendidikan'] = $ctr->MainModel->get_pendidikan_data();
		$data['kelas'] = $ctr->MainModel->get_kelas_data();
		$data['tahun'] = date('Y');
		$data['mode'] = 'Tambah';
		$ctr->load('view', 'siswa-tambah.html', $data);
	});
	
	$app->post('/tambah', function() use($app, $ctr) {
		// cek token dan load data
		$token 	= cek_token($ctr, $app); if ($token === false) redirect_index($app);
		$data 	= $ctr->MainModel->me($token);
		$ctr->load('model', 'siswa');
		$r = $ctr->SiswaModel->save();
		$url = ($r['type'] ? '/siswa?' : '/siswa/tambah?');
		$app->redirect($url . http_build_query($r));
	});
	
	$app->options('/:id', function() use($app) { $app->status(200); $app->stop(); });
	$app->get('/:id', function($id) use($app, $ctr) {
		// cek token dan load data
		$token 	= cek_token($ctr, $app); if ($token === false) redirect_index($app);
		$data 	= $ctr->MainModel->me($token);
		
		$data['mode'] = 'Edit';
		$ctr->load('model', 'siswa');
		$ctr->load('helper', 'date');
		$data['sekolah'] = $ctr->MainModel->get_school_data();
		$data['siswa'] = $ctr->SiswaModel->get_detail($id);
		$data['agama'] = $ctr->MainModel->get_agama_data();
		$data['jenistinggal'] = $ctr->MainModel->get_jenis_tinggal_data();
		$data['transport'] = $ctr->MainModel->get_alat_transport_data();
		$data['pendidikan'] = $ctr->MainModel->get_pendidikan_data();
		$data['kelas'] = $ctr->MainModel->get_kelas_data();
		$data['tahun'] = date('Y');
		$data['tahun'] = date('Y');
		$ctr->load('view', 'siswa-tambah.html', $data);
	})->conditions(array('id' => '[0-9]+'));
	
	$app->post('/:id', function($id) use($app, $ctr) {
		// cek token dan load data
		$token 	= cek_token($ctr, $app); if ($token === false) redirect_index($app);
		$ctr->load('model', 'siswa');
		$r = $ctr->SiswaModel->save($id);
		$app->redirect('/siswa?' . http_build_query($r));
	})->conditions(array('id' => '[0-9]+'));
});

// ------------------------------ MAPEL SECTION -------------------------------------
$app->options('/mapel', function() use($app) { $app->status(200); $app->stop(); });
$app->get('/mapel', function() use($app, $ctr) {
	// cek token dan load data
	$token 	= cek_token($ctr, $app); if ($token === false) redirect_index($app);
	$data 	= $ctr->MainModel->me($token);
	$data['sekolah'] = $ctr->MainModel->get_school_data();
	if (isset($_GET['type'])) {
		$data['notifikasi'] = array(
			'type' => $_GET['type'], 'pesan' => ( ! isset($_GET['pesan']) ? '' : $_GET['pesan'])
		);
	}
	$data['page'] = 'mapel';
	$ctr->load('view', 'mapel.html', $data);
});




/**
 * Group: api
 */
$app->group('/api', function() use ($app, $ctr) {
	$app->options('/guru', function() use($app) { $app->status(200); $app->stop(); });
	$app->get('/guru', function() use($app, $ctr) {
		$ctr->load('model', 'guru');
		$r = $ctr->GuruModel->get_list();
		json_output($app, $r);
	});
	
	$app->options('/siswa', function() use($app) { $app->status(200); $app->stop(); });
	$app->get('/siswa', function() use($app, $ctr) {
		$ctr->load('model', 'siswa');
		$ctr->load('helper', 'date');
		$r = $ctr->SiswaModel->get_list();
		json_output($app, $r);
	});
	
	/**
	 * Group: api/siswa
	 */
	$app->group('/siswa', function() use ($app, $ctr) {
		$app->options('/:id', function() use($app) { $app->status(200); $app->stop(); });
		$app->get('/:id', function($id) use($app, $ctr) {
			$ctr->load('model', 'siswa');
			$ctr->load('helper', 'date');
			$r = $ctr->SiswaModel->get_detail($id, false);
			json_output($app, $r);
		});
		
		
	 });
	
	/**
	 * Group: api/suggest
	 */
	$app->group('/suggest', function() use ($app, $ctr) {
		// ----------------------------------------------------------------
		/**
		 * Method: GET
		 * Verb: api/suggest/:field
		 */
		$app->options('/:field', function() use($app) { $app->status(200); $app->stop(); });
			$app->get('/:field', function($field) use($app, $ctr) {
			$ctr->load('model', 'main');
			$r = $ctr->MainModel->get_suggest($field);
			json_output($app, $r);
		});
	});
});
