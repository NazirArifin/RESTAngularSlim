<?php
/*!
 * file ini berperan sebagai loader
 * digunakan untuk meload helper, file, dan model
 */
namespace Lib;

class Loader {
	/**
	 * Folder atau direktori view
	 */
	private $direktori = '';
	
	/**
	 * Cache view
	 */
	private $cache = FALSE;
	
	// ----------------------------------------------------------------------------------------
	/**
	 * Jangan diedit setelah bagian ini
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
	
	/**
	 * Load controller dan router
	 */
	public function controller() {
		// set timezone
		date_default_timezone_set('Asia/Jakarta');
		
		// max execution time
		@set_time_limit(300);
		
		// twig template
		require_once 'lib/Twig/Autoloader.php';
		\Twig_Autoloader::register();
		
		$view = 'view' . ( ! empty($this->direktori) ? '/' . $this->direktori : '');
		if ($this->cache) {
			$this->twig = new \Twig_Environment(new \Twig_Loader_Filesystem($view), array(
				'cache' => 'config/cache'
			));
		} else {
			$this->twig = new \Twig_Environment(new \Twig_Loader_Filesystem($view));
		}
		$twig =& $this->twig;
		
		// router dengan Slim
		require_once 'lib/Slim/Slim.php';
		\Slim\Slim::registerAutoloader();
		$this->app = new \Slim\Slim();
		$this->load('helper', 'controller');
		$app =& $this->app;
		$ctr = $this;
		
		// custom 404
		$this->app->notFound(function() use ($twig) {
			print $twig->render('404.html', array());
		});
		// controller file
		foreach (scandir('controller') as $file) {
			if (is_file('controller/' . $file)) {
				require('controller/' . $file);
			}
		}
		
		$this->app->run();
	}
	
	/**
	 * Load database
	 */
	public function database() {
		static $host, $user, $pass, $dbnm, $drvr, $port;
		static $conn;
		if (empty($host)) {
			require 'config/dbconfig.php';
			$host = $dbconfig_host;
			$user = $dbconfig_username;
			$pass = $dbconfig_password;
			$dbnm = $dbconfig_database;
			$drvr = $dbconfig_driver;
			$port = $dbconfig_port;
		}
		
		if (is_null($conn)) {
			$data = array(
				'hostname' => $host,
				'username' => $user,
				'password' => $pass,
				'database' => $dbnm,
				'port' => $port
			);
			include 'lib/db_' . strtolower($drvr) . '.php';
			return $conn = new Db($data);
		}
		return $conn;
	}
	
	/**
	 * Load interface
	 */
	public function load() {
		switch (func_num_args()) {
			case 2:
				$param = func_get_arg(1);
				switch (func_get_arg(0)) {
					case 'model':
						$m = $this->model($param);
						$this->$m[0] = $m[1];
						break;
					case 'helper':
						$this->helper($param);
						break;
					case 'file':
						$this->file($param);
						break;
					case 'view':
						$this->view($param, array());
						break;
				}
				break;
			case 3:
				$this->view(func_get_arg(1), func_get_arg(2));
				break;
		}
	}
	
	/**
	 * Load model
	 */
	protected function model($m) {
		$model = 'model/' . $m . '_model.php';
		if ( ! is_file($model)) {
			$this->app->error();
		}
		require_once 'model/ModelBase.php';
		require_once $model;
		$m = str_replace(' ', '', ucwords(str_replace('_', ' ', $m)));
		$class = '\\Model\\' . $m . 'Model';
		return array($m . 'Model', new $class);
	}
	
	/**
	 * Load helper
	 */
	protected function helper($h) {
		require_once 'helper/' . $h . '_helper.php';
	}
	
	/**
	 * Load file
	 */
	protected function file($f) {
		require_once $f;
	}
	
	/**
	 * Load View
	 */
	protected function view($v, $p = array()) {
		print $this->twig->render($v, $p);
	}
}