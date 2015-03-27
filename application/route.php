<?php
/**
 *
 * @authors tiyee (tiyee@live.com)
 * @link http://www.tiyee.net
 * @date    2015-03-12 16:30:18
 * @version $Id$
 */



class Route {
	public static $action = '';
	public static $controller = '';
    public static $ob = null;
	public static  function init() {
		if(empty($_GET['c'])) {
			self::$controller = '\Controller\index';
		 } else {
			self::$controller = trim($_GET['c']);
			self::$controller = str_replace('/', '\\', self::$controller);
			self::$controller = '\Controller\\'.self::$controller;
		}
		if(empty($_GET['a'])) {
			self::$action = 'index';
		} else {
			self::$action = trim($_GET['a']);
		}
		try{
		  self::$ob = new self::$controller;
		  call_user_func(array(self::$ob,self::$action));

        } catch (\Exception\error $e) {
            $e->getErrInfo();

        }







	}
	static public function import($class) {
		//echo $class,'<br>';

        $filePath = str_replace('\\', '/', $class);
        $filePath = APP_PATH.'/'.$filePath.'.class.php';

        if(!is_file($filePath)) {

            throw new \Exception\error($filePath.' is not found', 404);

        }

        require($filePath);





	}


	static public function exceptionHandler($e) {
        echo 'uncaught exception  ',get_class($e);

    }


    static public function errorHandler($errno, $errstr,$errfile, $errline) {
      //  echo $errno, $errstr,$errfile, $errline;exit();


     $str = sprintf("<strong>%s</strong>: <font color='red'>%s</font> <font color='blue'>%d</font> lines of <font color='blue'>%s</font> [%s(%s)]",self::errTranslation($errno),$errstr,$errline,$errfile,PHP_VERSION,PHP_OS);
        if(defined('DEBUG')) {
            echo $str;
            echo '<hr>';
        } else {
            header('HTTP/1.1 404 Not Found');
            header('status: 404 Not Found');
            exit();
        }




    }
    static  private function errTranslation($errno) {
        switch ($errno) {
        case E_ERROR :
         return 'E_ERROR';
        case E_WARNING :
         return 'E_WARNING';
        case E_PARSE :
         return 'E_PARSE';
        case E_NOTICE :
         return 'E_NOTICE';
        case E_CORE_ERROR :
         return 'E_CORE_ERROR';
        case E_CORE_WARNING :
         return 'E_CORE_WARNING';
        case E_COMPILE_ERROR :
         return 'E_COMPILE_ERROR';
        case E_COMPILE_WARNING :
         return 'E_COMPILE_WARNING';
        case E_USER_ERROR :
         return 'E_USER_ERROR';
        case E_USER_WARNING :
         return 'E_USER_WARNING';
        case E_USER_NOTICE :
         return 'E_USER_NOTICE';
        case E_STRICT :
         return 'E_STRICT';
        case E_RECOVERABLE_ERROR :
         return 'E_RECOVERABLE_ERROR';
        case E_ALL :
         return 'E_ALL';

    default:
        return  "E_UNKONWN";
        break;
    }

    }

    static public function url($str,$arr = array(),$suffix = '') {
        if(empty($arr)) {
            return $str;
        }
        if(self::urlHooks($str,$arr,$suffix)) {
            return self::urlHooks($str,$arr,$suffix);
        }
        switch (PATH_INFO) {
            case 1:
                $condition = array();
                foreach($arr as $key => $value) {
                    $condition[] = urlencode($key).'='.urlencode($value);
                }
                $url = $str.'/?'.implode('&', $condition).$suffix;
                break;
            case 2:
                $condition = array();
                foreach($arr as $key => $value) {
                    if($key == 'a') {
                        $str .= '/'.$arr['a'];
                        continue;
                    }
                     if($key == 'c') {
                        $str .= '/'.$arr['c'];
                        continue;
                    }
                    $condition[] = urlencode($key).'/'.urlencode($value);
                }
                $url = $str.'/'.implode('/', $condition).$suffix;
                break;

            default:
                throw new \Exception\error('url mod error!', 500);

                break;
        }



        return $url;

    }
    static private function urlHooks($str,$arr = array(),$suffix = '') {
        if(empty($str) && isset($arr['a']) &&  $arr['a'] == 'post') {
            return '/post/'.$arr['id'].$suffix;
        }
        #other hooks
        return false;


    }
}
spl_autoload_register(array('Route', 'import'));
set_exception_handler('Route::exceptionHandler');
set_error_handler('Route::errorHandler', E_ALL);
//register_shutdown_function('Route::shutdownHandle');
