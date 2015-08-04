'use strict';

/* main controller */
app.controller('MainCtrl', function($scope, $http) {
	/**
	 * untuk pagination
	 */
	$scope.range = function(s, e) {
		var r = [];
		if ( ! e) { e = s; s = 0; }
		for (var i = s; i < e; i++) r.push(i);
		return r;
	};
	
	// untuk upload terserah
	$scope.file = null;
	
	// info siswa
	$scope.infoSiswa = {};
	$scope.setInfoSiswa = function(d) { $scope.infoSiswa = d; }
});

/* data guru */
app.controller('GuruCtrl', function($scope, $http) {
	$scope.guruList = [];
	$scope.cpage = 0;
	$scope.numpage = 0;
	$scope.loadData = function() {
		$http.get('/api/guru?cpage=' + $scope.cpage).
		success(function(d) { 
			$scope.guruList = d.guru; 
			$scope.numpage = d.numpage;
		});
	}; $scope.loadData();
	
	$scope.setPage = function() {
		if ($scope.cpage != this.n) { $scope.cpage = this.n; $scope.loadData(); }
	};
	$scope.prevPage = function() {
		if ($scope.cpage > 0) { $scope.cpage--; $scope.loadData(); }
	};
	$scope.nextPage = function() {
		if ($scope.cpage < $scope.numpage - 1) { $scope.cpage++; $scope.loadData(); }
	};
});

/* tambah guru */
app.controller('GuruTambahCtrl', function($scope) {
	$scope.guru = {};
	$scope.resetGuru = function() {
		$scope.guru = {
			id: 0, kepsek: false, nama: '', jenis: 1, nip: '', golongan: '', 
			jk: 'l', agama: '1', alamat: '', telepon: '',
			username: '', password: '', password2: ''
		};
		if ( ! angular.isUndefined(window.guru)) {
			for (var i in window.guru) {
				$scope.guru[i] = window.guru[i];
			}
		}
	}; $scope.resetGuru();
	$scope.getUsername = function() {
		$scope.guru.username = $scope.guru.nama.toLowerCase().replace(/[^a-z0-9]/, '').substr(0, 6);
	};
});

/* siswa */
app.controller('SiswaCtrl', function($scope, $http) {
	$scope.cpage = 0;
	$scope.numpage = 0;
	$scope.query = { nama: '', kelas: '' };
	$scope.siswaList = [];
	$scope.kelas = window.kelas;
	$scope.loadData = function() {
		$http.get('/api/siswa?cpage=' + $scope.cpage + '&' + jQuery.param($scope.query)).
		success(function(d) {
			$scope.siswaList = d.siswa;
			$scope.numpage = d.numpage;
			$scope.check = false;
		});
	}; $scope.loadData();
	
	$scope.check = false;
	$scope.checkAll = function() {
		$scope.check = ! $scope.check;
		for (var i = 0; i < $scope.siswaList.length; i++) {
			$scope.siswaList[i].check = $scope.check;
		}
	};
});

/* tambah siswa */
app.controller('SiswaTambahCtrl', function($scope) {
	$scope.bulan = [{i:1,s:'Januari'}, {i:2,s:'Pebruari'}, {i:3,s:'Maret'}, {i:4,s:'April'}, {i:5,s:'Mei'}, {i:6,s:'Juni'}, {i:7,s:'Juli'}, {i:8,s:'Agustus'}, {i:9,s:'September'}, {i:10,s:'Oktober'}, {i:11,s:'November'}, {i:12,s:'Desember'}];
	$scope.siswa = {};
	$scope.ayah = {};
	$scope.ibu = {};
	$scope.wali = {};
	$scope.tahun = window.tahun;
	$scope.resetSiswa = function() {
		$scope.siswa = {
			id: 0, nama: '', kelas: '', nis: '', nisn: '', tempat: '', tanggal: 1, bulan: 1, tahun: $scope.tahun - 4,
			jk: 'l', agama: '1', masuk: '', pendidikan: 1, telepon: '', handphone: '', email: '', alamat: '',
			rt: '', rw: '', dusun: '', desa: '', kecamatan: '', kdpos: '', kabupaten: '', provinsi: '',
			jenistinggal: '1', transport: '1'
		};
		if ( ! angular.isUndefined(window.siswa)) {
			$scope.siswa = window.siswa.siswa;
		}
	}; $scope.resetSiswa();
	$scope.resetAyahIbuWali = function() {
		$scope.ayah = {
			id: 0, nama: '', tahun: $scope.tahun - 25, pendidikan: '1', pekerjaan: '', penghasilan: ''
		};
		$scope.ibu = {
			id: 0, nama: '', tahun: $scope.tahun - 25, pendidikan: '1', pekerjaan: '', penghasilan: ''
		};
		$scope.wali = {
			id: 0, nama: '', tahun: $scope.tahun - 25, pendidikan: '1', pekerjaan: '', penghasilan: '', alamat: ''
		};
		if ( ! angular.isUndefined(window.siswa)) {
			$scope.ayah = window.siswa.ayah;
			$scope.ibu = window.siswa.ibu;
			$scope.wali = window.siswa.wali;
		}
	}; $scope.resetAyahIbuWali();
});
