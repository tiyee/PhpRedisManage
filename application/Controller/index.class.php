<?php
/**
 *
 * @authors tiyee (tiyee@live.com)
 * @link http://www.tiyee.net
 * @date    2015-03-12 16:37:51
 * @version $Id$
 */
namespace Controller;
use \Core\base;
class index  extends base {

	public $keys = array();
	public $it = null;
    public function index() {

    	/*$this->redis->set('mytest','mytest');
    	echo $this->redis->get('mytest');*/


    	$this->assign['databases'] = $this->redis->db_num;
        $database = $this->request->get('db','int');
    	$this->assign['database'] =  empty($database)?0:$database;
    	$this->it = $this->getKeysLimit();
    	$this->assign['keys'] = $this->keys;
    	//print_r($this->assign);exit();
    	$this->display('index');
    }
    public function json_keyLimit() {

    	$it = empty($this->request->get['it'])?null:$this->request->get('it',1);

    	$json = array();
    	$json['it'] = $this->getKeysLimit($it);
    	$json['values'] = $this->keys;
    	$this->return_json($json);
    }
    public function getKeysLimit($it = null) {
    	$this->redis->setOption(\Redis::OPT_SCAN, \Redis::SCAN_RETRY); /* retry when we get no keys back */
    	$this->keys = array();
    	$arr_keys = array();
    	$arr = array();
    	//$arr_keys = $this->redis->scan($it);
    	//print_r($it);
    	$limit = 5;
    	while(($arr_keys = $this->redis->scan($it)) && ($limit > 0) ){
			    foreach($arr_keys as $str_key) {
			        //$arr = $str_key;
			        $arr[] = $this->info($str_key);


			    }
			    $limit--;
			    //echo "No more keys to scan!$it\n";
		}
		$this->keys = $arr;
		return $it ;
    }
    public function test() {
	    	$it = NULL; /* Initialize our iterator to NULL */
			$this->redis->setOption(\Redis::OPT_SCAN, \Redis::SCAN_RETRY); /* retry when we get no keys back */
			while($arr_keys = $this->redis->scan($it)) {
			    foreach($arr_keys as $str_key) {
			        $this->assign['keys'][] = $str_key;
			    }
			    echo "No more keys to scan!$it\n";
			}

    }

}
