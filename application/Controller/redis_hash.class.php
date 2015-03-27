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
class  redis_hash extends base {



    public function hSet() {
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
        if(empty($this->request->post('field','trim'))) {
            $json['msg'] = 'the  field is empty!!';
            $this->return_json($json);
        } else {
            $field = $this->request->post('field','trim');
        }
        if(!isset($this->request->post['value'])) {
            $json['msg'] = 'the  value is empty!!';
            $this->return_json($json);
        } else {
            $value = $this->request->post['value'];
        }


        $result = $this->redis->hSet($key, $field, $value);
        switch ($result) {
            case 1:
                $json['error'] = 0;
                $json['msg'] = ' value didn\'t exist and was added successfully';
                break;
            case 0:
                $json['error'] = 0;
                $json['msg'] = 'the value was already present and was replaced';
                break;
            case false:
                $json['error'] = 1;
                $json['msg'] = 'there was an error.';
                break;


            default:
                $json['error'] = 1;
                $json['msg'] = 'unkown error.';
                break;
        }
         $this->return_json($json);

    }
    public function hSetNx() {
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
        if(empty($this->request->post('field','trim'))) {
            $json['msg'] = 'the  field is empty!!';
            $this->return_json($json);
        } else {
            $field = $this->request->post('field','trim');
        }
        if(!isset($this->request->post['value'])) {
            $json['msg'] = 'the  value is empty!!';
            $this->return_json($json);
        } else {
            $value = $this->request->post['value'];
        }


        if(false == $this->redis->hSetNx($key, $field, $value) ) {
            $json['msg'] = 'it was already present!!';

        } else {
            $json['error'] = 0;
            $json['msg'] = ' add success';
        }

         $this->return_json($json);

    }

    public function hDel() {
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
        if(!isset($this->request->post['field'])) {
            $json['msg'] = 'the  field is empty!!';
            $this->return_json($json);
        } else {
            $field = $this->request->post('field','trim');
        }
        if(false == $this->redis->hDel($key,$field)) {
            $json['msg'] = ' the hash table  or the field doesn\'t exist';
        } else {
             $json['error'] = 0;
             $json['msg'] = ' delete the field success';
        }
        $this->return_json($json);
    }

    public function getInfo() {
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
               // 'class' => ($i%2 == 0)?' class="pure-table-odd"':''
                );
            $i ++;
        }

        $info['size']   = count($info['values']);
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


}
