<?php
/**
 *
 * @authors tiyee (tiyee@live.com)
 * @link http://www.tiyee.net
 * @date    2015-03-12 16:43:00
 * @version $Id$
 */
namespace Core;

class base  {
    protected $db = 0;
    public function __construct(){
      if(!empty($this->request->get('db',1))) {
        $this->db = $this->request->get('db',1);
      }

      if(false == $this->redis->select($this->db)) {
        throw new \Exception("Error Processing Request");

      }
      $this->assign['dbsize'] = $this->redis->dbsize();
    }


    public function __get($key) {
      switch ($key) {
      	case 'redis':
      		$this->$key = \Library\redis::getInstance();
      		break;
        case 'request':
          $this->$key = \Library\request::getInstance();
          break;

      	default:
      		 throw new \Exception\error(' Library:'.$key.' is not defined');
      		break;
      }

      return $this->$key ;
   }






  protected $assign = array();
  public function info($key) {
    $arr = array();
    $arr['type'] = $this->type($this->redis->type($key));
    $arr['key'] = $key;
    return $arr;

  }

  protected function type($type = 0) {
    switch ($type) {
      case \Redis::REDIS_STRING:
        $name = 'string';
        break;
      case \Redis::REDIS_HASH:
        $name = 'hash';

        break;
      case \Redis::REDIS_LIST:
        $name = 'list';
        break;
      case \Redis::REDIS_SET:
        $name = 'set';
        break;
      case \Redis::REDIS_ZSET:
        $name = 'zset';
        break;


      default:
        $name = 'unkown';

        break;
    }
    return $name;
  }
  protected function key_exists($key = '') {
        if(empty($key)) return false;
        return $this->redis->exists($key);
    }
    protected function getKey() {
        return $this->request->get('key','trim');
    }
    public function renameKey() {
        $json = array('error' => 1,'msg'=>'error');
        if(empty($this->request->post('oKey','trim'))) {
            $json['msg'] = 'the old key is empty!!';
            $this->return_json($json);
        } else {
            $oKey = $this->request->post('oKey','trim');
        }
        if(empty($this->request->post('nKey','trim'))) {
            $json['msg'] = 'the new key is empty!!';
            $this->return_json($json);
        } else {
            $nKey = $this->request->post('nKey','trim');
        }

        if(!$this->key_exists($oKey)) {
             $json['msg'] = 'the old key is not exists!!';
            $this->return_json($json);
        }
        if(!preg_match('|^[\w\d\-\.]+$|i', $nKey)) {
            $json['msg'] = 'the new key is is unvalid!!';
            $this->return_json($json);
        }
        if(false == $this->redis->renameNx($oKey,$nKey)) {

             $json['msg'] = 'renameNx false !!';
             $this->return_json($json);
        }
        $json['error'] = 0;
        $json['msg'] = 'renameNx success !!';
        $this->return_json($json);


    }

    public function setTimeout() {
        $json = array('error' => 1,'msg'=>'error');
        if(empty($this->request->post('key','trim'))) {
            $json['msg'] = 'the  key is empty!!';
            $this->return_json($json);
        } else {
            $key = $this->request->post('key','trim');
        }
        if(!$this->key_exists($key)) {
             $json['msg'] = 'the  key is not exists!!';
             $this->return_json($json);
        }
        if($this->request->post('ttl',1) < -1) {
            $json['msg'] = 'the new ttl is is unvalid!!';
            $this->return_json($json);
        } elseif ($this->request->post('ttl',1) == -1) {
            //$ttl = -1;
            $this->persist($key,$json);
        } else {
            $ttl = $this->request->post('ttl',1);

        }

        if(false == $this->redis->setTimeout($key,$ttl)) {

             $json['msg'] = 'setTimeout false !!';
             $this->return_json($json);
        }
        $json['error'] = 0;
        $json['msg'] = 'setTimeout success !!';
        $this->return_json($json);


    }
    private function persist($key,$json) {
      if(false == $this->redis->persist($key)) {
          $json['msg'] = 'the key didn’t exist or didn’t have an expiration ';

      } else {
          $json['error'] = 0;
          $json['msg'] = 'timeout was removed !!';
      }
      $this->return_json($json);
    }
    public function deleteKey() {
        $json = array('error' => 1,'msg'=>'error');
        $key = $this->getKey();
        if(false == $this->key_exists($key)) {
            $json['msg'] = 'the key :'.$key.' is not exists';
            $this->return_json($json);
        }
        if($this->redis->delete($key)) {
            $json['error'] = 0;
            $json['msg'] = 'success !';
        } else {
            $json['msg'] = 'nothing is deleted !';
        }
         $this->return_json($json);

    }


	protected function redirect($url, $status = 302) {
		header('Status: ' . $status);
		header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url));
		exit();
	}
    /**
     *  get the content of page
     *  @return [type] [description]
     */
	protected function fetch($file = '') {

        $file = APP_PATH . '/Template/' . $file . '.php';
		if (is_file($file)) {


			extract($this->assign);

       //   print_r(get_defined_vars());
      		ob_start();
           include $file;


	  		$content = ob_get_contents();

      		ob_end_clean();

      		return $content;
    	} else {

			throw new \Exception\error(' Could not load template ' . $file . '!', 500);
    	}
	}
	protected function display($file){
		echo $this->fetch($file);
		exit();

	}

	protected function ajax_return($arr) {
		echo json_encode($arr);
		exit();
	}
  public function return_json($arr = array()) {
        header("Content-Type : application/json");
        header("Access-Control-Allow-Origin : *");
        $arr['ip'] = $this->request->ip();
        echo json_encode($arr);
        exit();
    }

}
