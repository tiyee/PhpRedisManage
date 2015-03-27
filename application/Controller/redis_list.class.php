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
class  redis_list extends base {


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
    public function lSet() {
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
        if(!isset($this->request->post['index'])) {
            $json['msg'] = 'the  index is empty!!';
            $this->return_json($json);
        } else {
            $index = $this->request->post('index',1);
        }
        if(!isset($this->request->post['value'])) {
            $json['msg'] = 'the  value is empty!!';
            $this->return_json($json);
        } else {
            $value = $this->request->post['value'];
        }

        //echo $index;exit();
        $result = $this->redis->lSet($key, $index, $value);
        switch ($result) {
            case true:
                $json['error'] = 0;
                $json['msg'] = ' the new value is setted';
                break;

            case false:
                $json['error'] = 1;
                $json['msg'] = ' the index is out of range, or data type identified by key is not a list';
                break;


            default:
                $json['error'] = 1;
                $json['msg'] = 'unkown error.';
                break;
        }
         $this->return_json($json);

    }


    public function lRem() {
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
        if(!isset($this->request->post['index'])) {
            $json['msg'] = 'the  index is empty!!';
            $this->return_json($json);
        } else {
            $index = $this->request->post('index',1);
        }
        $value = time();
        $this->redis->lSet($key, $index, $value);

        if(false == $this->redis->lRem($key, $value, 1)) {
            $json['msg'] = ' the value identified by key is not a list';
        } else {
             $json['error'] = 0;
             $json['msg'] = ' delete the index success';
        }
        $this->return_json($json);
    }

    public function getInfo() {
        $info = array();
        $info['exists'] = 1;
        $info['key'] = $this->getKey();
        $info['type'] = 'list';
        if(false == $this->key_exists($info['key'])) {
            $info['exists'] = 0;
        }
        $info['ttl'] = $this->redis->ttl($info['key']);

        $info['size']   = $this->redis->lLen($info['key']);
         $info['page'] = $info['current'] = 1;
        $info['limit'] = 20;
        $values = $this->redis->lRange($info['key'],0,$info['limit'] - 1);
        $info['values'] = array();
        foreach($values as $value) {
            $info['values'][] = array(
                'value' => $value,
                'isEdit' => true,
                'nValue' => $value

                );
        }
        $info['pages'] = ceil($info['size']/$info['limit']);


        //$info['pages'] = ceil($info['size']/$info['limit']);



        return $this->return_json($info);

    }
    public function getLimit() {
        $info = array();
        $info['exists'] = 1;
        $info['key'] = $this->getKey();
        $info['type'] = 'list';
        if(false == $this->key_exists($info['key'])) {
            $info['exists'] = 0;
        }
        $info['ttl'] = $this->redis->ttl($info['key']);
        $limit = 20;
        $p = empty($this->request->get('p',1))?1:$this->request->get('p',1);
        $start = ($p - 1)*$limit;
        $end = $p * $limit -1;
        $info['page'] = $info['current'] = $p;
        $values = $this->redis->lRange($info['key'],$start,$end);
        $info['values'] = array();
        foreach($values as $value) {
            $info['values'][] = array(
                'value' => $value,
                'isEdit' => true,
                'nValue' => $value

                );
        }

        $info['size']   = $this->redis->lLen($info['key']);
        $info['limit'] = 20;
       $info['pages'] = ceil($info['size']/$info['limit']);

        return $this->return_json($info);
    }



}
