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
class  redis_zset extends base {


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
    public function zSet() {
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
        if(!isset($this->request->post['score'])) {
            $json['msg'] = 'the  score is empty!!';
            $this->return_json($json);
        } else {
            $score = $this->request->post('score',1);
        }
        if(!isset($this->request->post['value'])) {
            $json['msg'] = 'the  value is empty!!';
            $this->return_json($json);
        } else {
            $value = $this->request->post['value'];
        }
        if(!isset($this->request->post['nValue'])) {
            $json['msg'] = 'the  nValue is empty!!';
            $this->return_json($json);
        } else {
            $nValue = $this->request->post['nValue'];
        }
        $this->redis->zDelete($key, $value);



        $result = $this->redis->zAdd($key, $score, $nValue);
        switch ($result) {
            case 1:
                $json['error'] = 0;
                $json['msg'] = ' the new value is setted';
                break;

            case 0:
                $json['error'] = 1;
                $json['msg'] = ' false ';
                break;


            default:
                $json['error'] = 1;
                $json['msg'] = 'unkown error.';
                break;
        }
         $this->return_json($json);

    }


    public function zRem() {
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
        if(empty($this->request->post['value'])) {
            $json['msg'] = 'the value is empty!!';
            $this->return_json($json);
        } else {
            $value = $this->request->post('value','trim');
        }


        if(0 == $this->redis->zRem($key, $value)) {
            $json['msg'] = ' delete failed';
        } else {
             $json['error'] = 0;
             $json['msg'] = ' delete the score success';
        }
        $this->return_json($json);
    }

    public function getInfo() {
        $info = array();
        $info['exists'] = 1;
        $info['key'] = $this->getKey();
        $info['type'] = 'set';
        if(false == $this->key_exists($info['key'])) {
            $info['exists'] = 0;
        }
        $info['ttl'] = $this->redis->ttl($info['key']);

        $info['size']   = $this->redis->zSize($info['key']);
        $info['current'] = 1;
        $info['limit'] = 20;
        $it = isset($this->request->post['it'])?$this->request->post('it',1):-1;
        $info['pre_it'] = $it;
        $pattern = empty($this->request->post('keyword','trim'))?'':$this->request->post('keyword','trim');
        $this->getLimit($it,$info,$pattern);




        return $this->return_json($info);

    }
    public function search() {
         $pattern = empty($this->request->post('keyword','trim'))?'':$this->request->post('keyword','trim');
        $info = array();
        $info['exists'] = 1;
        $info['key'] = $this->getKey();
        $info['type'] = 'set';
        if(false == $this->key_exists($info['key'])) {
            $info['exists'] = 0;
        }
        $info['ttl'] = $this->redis->ttl($info['key']);

        $info['size']   = $this->redis->zSize($info['key']);
        $info['current'] = 1;
        $info['limit'] = 20;

        $it = isset($this->request->post['it'])?$this->request->post('it',1):-1;

        $this->getLimit($it,$info,$pattern);
    }


    public function getLimit($it,$info,$pattern='') {
            if($it < 0 ) {
                $it = NULL;
            }
            $limit = 5;
            $arr = array();
            $this->redis->setOption(\Redis::OPT_SCAN, \Redis::SCAN_RETRY);
            while($limit > 0 && ($arr_mems = $this->redis->zscan($info['key'], $it,'*'.$pattern.'*')) ) {
                foreach($arr_mems as  $str_mem => $f_score) {
                    //echo "Member: $str_mem\n";
                    $info['values'][] = array(
                        'value' => $str_mem,
                        'score' => $f_score,
                        'nScore' => $f_score,
                        'isEdit' => true,
                        'nValue' => $str_mem

                     );
                }
                $limit--;
            }

            $info['it'] = $it;
            $info['pattern'] = $pattern;
             return $this->return_json($info);

    }



}
