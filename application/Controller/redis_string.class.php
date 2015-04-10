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
class  redis_string extends base {



    public function set() {
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

        if(!isset($this->request->post['value'])) {
            $json['msg'] = 'the  value is empty!!';
            $this->return_json($json);
        } else {
            $value = $this->request->post['value'];
        }


        $result = $this->redis->set($key, $value);
        switch ($result) {
            case true:
                $json['error'] = 0;
                $json['msg'] = ' the new value is setted';
                break;



            default:
                $json['error'] = 1;
                $json['msg'] = 'unkown error.';
                break;
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

        $info['size']   = $this->redis->strlen($info['key']);

        $info['values'] = $this->redis->get($info['key']);
        $info['isEditValue'] = true;

        //$info['pages'] = ceil($info['size']/$info['limit']);



        return $this->return_json($info);

    }



}
