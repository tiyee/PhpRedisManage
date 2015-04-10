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
        $key = $this->request->post('key','trim');
        if(empty($key)) {
            $json['msg'] = 'the  key is empty!!';
            $this->return_json($json);
        }
        if(!$this->key_exists($key)) {
             $json['msg'] = 'the  key is not exists!!';
             $this->return_json($json);
        }

        $field = $this->request->post('field','trim');
        if(empty($field)) {
            $json['msg'] = 'the  field is empty!!';
            $this->return_json($json);
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
        $key = $this->request->post('key','trim');
        if(empty($key)) {
            $json['msg'] = 'the  key is empty!!';
            $this->return_json($json);
        }

        if(!$this->key_exists($key)) {
             $json['msg'] = 'the  key is not exists!!';
             $this->return_json($json);
        }

        $field = $this->request->post('field','trim');
        if(empty($field)) {
            $json['msg'] = 'the  field is empty!!';
            $this->return_json($json);
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
        $key = $this->request->post('key','trim');
        if(empty($key)) {
            $json['msg'] = 'the  key is empty!!';
            $this->return_json($json);
        }

        if(!$this->key_exists($key)) {
             $json['msg'] = 'the  key is not exists!!';
             $this->return_json($json);
        }
        $field = $this->request->post('field','trim');
        if(empty($field)) {
            $json['msg'] = 'the  field is empty!!';
            $this->return_json($json);
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
                'value' => $value
                );
            $i ++;
        }

        $info['size']   = count($info['values']);
        return $this->return_json($info);

    }



}
