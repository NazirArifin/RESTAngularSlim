<?php
/*!
 * file ini berperan sebagai loader
 * digunakan untuk meload helper, file, dan model
 */
namespace Lib;

class Loader {
	/**
	 * loader sebenarnya, menyiapkan seluruh sistem dan menjalankan slim
	 * @return void
	 */
	public function controller() {
		// starting session
		session_cache_limiter(false);
		session_start();

		// load configs		
		require_once 'config/envconfig.php';
		$this->salt = $salt;

		// set timezone
		date_default_timezone_set($timezone);
		
		// twig template
		require_once 'lib/Twig/Autoloader.php';
		\Twig_Autoloader::register();
		
		$view = 'view' . ( ! empty($this->direktori) ? '/' . $this->direktori : '');
		// cache atau tidak
		if ($cache_view) {
			$this->twig = new \Twig_Environment(new \Twig_Loader_Filesystem($view), array(
				'cache' => 'cache'
			));
		} else {
			$this->twig = new \Twig_Environment(new \Twig_Loader_Filesystem($view));
		}
		$twig =& $this->twig;
		
		// router dengan Slim
		require_once 'lib/Slim/Slim.php';
		\Slim\Slim::registerAutoloader();
		$this->app = new \Slim\Slim(
			array(
				'debug'	=> $debug
			)
		);
		$this->load('helper', 'controller');
		$app =& $this->app;
		$ctr = $this;
		
		// custom 404
		$this->app->notFound(function() use ($twig) {
			print $twig->render('404.html', array());
		});
		
		// auto load library
		spl_autoload_register(function($class) {
			require_once 'lib/' . str_replace('Lib\\', '', $class) . '.php';
		});
		
		// load semua controller file
		foreach (scandir('controller') as $file) {
			if (is_file('controller/' . $file)) {
				require('controller/' . $file);
			}
		}
		
		$this->app->run();
	}

	/**
	 * salt untuk password
	 * @var string
	 */
	private $salt = '';
	
	/**
	 * load database secara otomatis
	 * @return void
	 */
	public function database() {
		static $host, $user, $pass, $dbnm, $drvr, $port;
		static $conn;
		if (empty($host)) {
			require_once 'config/dbconfig.php';
			$host = $dbconfig_host;
			$user = $dbconfig_username;
			$pass = $dbconfig_password;
			$dbnm = $dbconfig_database;
			$drvr = $dbconfig_driver;
			$port = $dbconfig_port;
		}
		
		if (is_null($conn)) {
			$data = array(
				'hostname' 	=> $host,
				'username' 	=> $user,
				'password' 	=> $pass,
				'database' 	=> $dbnm,
				'port' 			=> $port
			);
			
			// include driver di setting
			include 'lib/DbDriver/db_' . strtolower($drvr) . '.php';
			return $conn = new Db($data);
		}
		return $conn;
	}
	
	/**
	 * fungsi untuk meload model, view, helper dan file
	 * @param  string $type   jenis resource yang diload
	 * @param  string $param  parameter yang dilewatkan
	 * @param  mixed $param2  parameter tambahan, biasanya berupa array
	 * @return void
	 */
	public function load($type, $param, $param2 = null) {
		switch ($type) {
			case 'model':
				$m = $this->load_model($param);
				$this->$m[0] = $m[1];
				break;
			case 'helper':
				$this->load_helper($param);
				break;
			case 'file':
				$this->load_file($param);
				break;
			case 'view':
				$this->load_view($param, $param2);
				break;
			case 'lib':
				return $this->load_lib($param);
				break;
		}
	}

	/**
	 * shortcut untuk load model
	 * @param  string $param nama model
	 * @return array         hasil pembuatan model baru
	 */
	public function model($param) {
		$m = $this->load_model($param);
		$this->$m[0] = $m[1];
	}

	/**
	 * shortcut untuk load helper
	 * @param  string $param nama helper
	 * @return void
	 */
	public function helper($param) {
		$this->load_helper($param);
	}
	
	/**
	 * shortcut untuk load file
	 * @param  string $param path file yang akan diinclude
	 * @return void
	 */
	public function file($param) {
		$this->load_file($param);
	}

	/**
	 * shortcut untuk load view
	 * @param  string $view  nama view di folder view
	 * @param  array  $param data yang dilewatkan ke view
	 * @return void
	 */
	public function view($view, $param) {
		$this->load_view($view, $param);
	}

	/**
	 * shortcut untuk load lib
	 * @param  string $param nama class
	 * @return object        instance dari class
	 */
	public function lib($param) {
		return $this->load_lib($param);
	}

	/**
	 * __get method
	 * @param  string $name nama property
	 * @return mixed       isi property
	 */
	public function __get($name) {
		if ( ! isset($this->$name)) {
			trigger_error('Undefined property ' . $name, E_USER_NOTICE);
			$this->app->halt(500, 'User Noticed');
		}
		return $this->$name;
	}

	/**
	 * Load model
	 */
	protected function load_model($m) {
		$model = 'model/' . $m . '_model.php';
		if ( ! is_file($model)) {
			$this->app->halt(500, 'Cant load Model');
			$this->app->stop();
		}
		require_once 'model/ModelBase.php';
		require_once $model;
		$class = '\\Model\\' . ucfirst($m) . 'Model';
		return array(ucfirst($m) . 'Model', new $class);
	}
	
	/**
	 * Load helper
	 */
	protected function load_helper($h) {
		require_once 'helper/' . $h . '_helper.php';
	}
	
	/**
	 * Load file
	 */
	protected function load_file($f) {
		require_once $f;
	}
	
	/**
	 * Load View
	 */
	protected function load_view($v, $p) {
		print $this->twig->render($v, $p);
	}

	/**
	 * load library
	 * @param  string $l nama class di lib
	 * @return object    instance dari class
	 */
	protected function load_lib($l = '') {
		require_once 'lib/' . $l. '.php';
		$class = '\\Lib\\' . $l;
		return new $class();
	}

	/**
	 * construct dijadikan private biar hanya ada satu instance
	 */
	private function __construct() {}
	private static $instance;
	public static function get_instance() {
		if (is_null(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
}