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
class process  extends base {

    private function key_exists($key = '') {
        if(empty($key)) return false;
        return $this->redis->exists($key);
    }
    private function getKey() {
        return $this->request->get('key','trim');
    }
    public function redis_hash() {
        $info = array();
        $info['exists'] = 1;
        $info['key'] = $this->getKey();
        $info['type'] = 'hash';
        if(false == $this->key_exists($info['key'])) {
            $info['exists'] = 0;
        }
        $info['ttl'] = $this->redis->ttl($info['key']);
        $values = $this->redis->hGetAll($info['key']);
        $i = 0;
        foreach($values as $field => $value) {
            $info['values'][] = array(
                'field' => $field,
                'value' => $value,
                'class' => ($i%2 == 0)?' class="pure-table-odd"':''
                );
            $i ++;
        }

        $info['size']   = count($info['values']);
        return $this->return_json($info);

    }
    public function redis_list() {
        $info = array();
        $info['exists'] = 1;
        $info['key'] = $this->getKey();
        $info['type'] = 'list';
        if(false == $this->key_exists($info['key'])) {
            $info['exists'] = 0;
        }
        $info['ttl'] = $this->redis->ttl($info['key']);
        $info['values'] = $this->redis->lRange($info['key'],0,-1);

        $info['size']   = $this->redis->lLen($info['key']);
        return $this->return_json($info);

    }
    public function redis_zset() {
        $info = array();
        $info['exists'] = 1;
        $info['key'] = $this->getKey();
        $info['type'] = 'zset';
        if(false == $this->key_exists($info['key'])) {
            $info['exists'] = 0;
        }
        $info['ttl'] = $this->redis->ttl($info['key']);
        $arr = $this->redis->zRange($info['key'], 0, -1, TRUE);//zRange('key1', 0, -1, true);
        foreach($arr as $val => $score) {
            $info['values'][] = array(
                'val' =>$val,
                'score' => $score

                );
        }
        //$info['arr'] = $arr;
        $info['size']   = $this->redis->zCard($info['key']);
        return $this->return_json($info);

    }
    public function redis_set() {
        $info = array();
        $info['exists'] = 1;
        $info['key'] = $this->getKey();
        $info['type'] = 'set';
        if(false == $this->key_exists($info['key'])) {
            $info['exists'] = 0;
        }
        $info['ttl'] = $this->redis->ttl($info['key']);
        $info['values'] = $this->redis->sMembers($info['key']);

        $info['size']   = $this->redis->sCard($info['key']);
        return $this->return_json($info);
    }
    public function redis_string() {
        $info = array();
        $info['exists'] = 1;
        $info['key'] = $this->getKey();
        $info['type'] = 'string';
        if(false == $this->key_exists($info['key'])) {
            $info['exists'] = 0;
        }
        $info['ttl'] = $this->redis->ttl($info['key']);
        $info['values'] = $this->redis->get($info['key']);

        $info['size']   = $this->redis->strlen($info['key']);
        return $this->return_json($info);
    }

	public function return_json($arr = array()) {
        header("Content-Type : application/json");
        header("Access-Control-Allow-Origin : *");
        echo json_encode($arr);
        exit();
    }
}
