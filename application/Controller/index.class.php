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
    public function add() {
        $this->redis->delete('zset');
           for($a=65;$a<=90;$a++)
            for($b=65;$b<=90;$b++)
                for($c=65;$c<=90;$c++)
                    for($d=65;$d<=90;$d++)
                        for($e=65;$e<=90;$e++)
                            for($f=65;$f<=90;$f++)
                                for($g=65;$g<=90;$g++)
                                    for($h=65;$h<=90;$h++)
                                        $this->redis->zAdd('zset',$a+1*$b+2*$c+3*$d+4*$e+5*$f+6*$g+7*$h+8*$c+9*$h,chr($a).chr($b).chr($c).chr($d).chr($e).chr($f).chr($g).chr($h));

           echo 'finished';
    }
}
