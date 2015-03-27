<?php
namespace Library;
class request {
	private static $_instance;
    /*private $get = array();
	private $post = array();
	private $cookie = array();
	private $request = array();
	private $files = array();
	private $server = array();
	private $ip = '';
	private $ip2long = 0;*/
	private function __construct(){}
	private function __clone(){}
	public static function getInstance() {
		if(! (self::$_instance instanceof self) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __get($key) {
		$key = strtolower($key);
		/*if(isset($this->$key)) {
			return $this->$key;
		}*/
		$value = array();
		switch ($key) {
			case 'get':
				$value = $_GET;
				echo '赋值一次';
				break;
			case 'post':
				$value = $_POST;
				break;
			case 'cookie':
				$value = $_COOKIE;
				break;
			case 'request':
				$value = $_REQUEST;
				break;
			case 'files':
				$value = $_FILES;
				break;
			case 'server':
				$value = $_SERVER;
				break;
			case 'ip' :
				$value = $this->getIp();
				break;
			case 'ip2long' :
				$value = $this->getIp(1);
				break;



			default:
				 throw new \tiyeePHP\Exception\error($key.' is not defined ', 500);
				break;
		}
		return $value;

	}
	public function __set($key,$value) {
		$this->$key = $value;
	}
	public function get($key,$mod) {
		if(!isset($this->get)) {
			$this->get = $_GET;
		}
		if(!isset($this->get[$key])) {
			return false;
		}
		switch ($mod) {
			case 1:
			case 'int':
				return (int)$this->get[$key];
				break;
			case 2:
			case 'trim':

				return trim($this->get[$key]);
				break;
			case 3:
			case 'addslashes':
				return addslashes($this->get[$key]);
			case 4:
			case 'htmlspecialchars':
				return htmlspecialchars($this->get[$key]);
				break;
			default:
				 throw new \Exception\error($mode.' is not defined ', 500);
				break;
		}

	}
	public function post($key,$mod) {
		if(!isset($_POST[$key])) {
			return false;
		}
		switch ($mod) {
			case 1:
			case 'int':
				return (int)$_POST[$key];
				break;
			case 2:
			case 'trim':
				return trim($_POST[$key]);
				break;
			case 3:
			case 'addslashes':
				return addslashes($_POST[$key]);
			case 4:
			case 'htmlspecialchars':
				return htmlspecialchars($_POST[$key]);
				break;
			default:
				 throw new \Exception\error($mode.' is not defined ', 500);
				break;
		}

	}
	public function ip($mod = 0) {
		if(!$this->getIp()) {
			return false;
		}
		if(0 === $mod) {
			return $this->getIp();
		}
		switch ($mod) {
			case 'int':
				return ip2long($this->ip);
				break;

			default:
				# code...
				break;
		}
	}

	private function getIp($mod = 0) {
		if(!empty($_SERVER["HTTP_CLIENT_IP"]))
			$cip = $_SERVER["HTTP_CLIENT_IP"];
	   else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
	   		$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	   else if(!empty($_SERVER["REMOTE_ADDR"]))
	   		$cip = $_SERVER["REMOTE_ADDR"];
	   else
	   		$cip = false;
	   	if($mod) return ip2long($cip);
	   return $cip;
	}



  	private static function filter($data) {
    	if (is_array($data)) {
	  		foreach ($data as $key => $value) {
				unset($data[$key]);

	    		$data[self::filter($key)] = self::filter($value);
	  		}
		} else {
	  		$data = htmlspecialchars($data, ENT_COMPAT);
		}

		return $data;
	}
	public function test() {
		echo 'request test';
	}
}
